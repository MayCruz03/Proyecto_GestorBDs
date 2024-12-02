const createTableModule = {
    grdColumnsBody: $("#tbl-columns tbody"),
    dataTypes: [],
    tables: [],
    tablesColumns: [],
    async init() {
        const _this = this;
        await this.fillDataTypes();
        await this.fillTableObjects();

        $("#btn-add-rows").on("click", function () {
            const numRows = parseInt($("#txt-number-rows").val());
            _this.addRows(numRows);
        });

        $("#btn-create-table").on("click", async function () {
            await _this.constructTable();
        })

        this.grdColumnsBody.empty();
        this.addRows(2);
    },
    initializeEvents() {
        $(this.grdColumnsBody).find(".ui.checkbox").checkbox();
        $(this.grdColumnsBody).find(":input[data-input-mask]").each(function () {
            const options = $(this).data("input-mask");
            $(this).inputmask(options);
        });
    },
    addRows(numRows) {
        for (let i = 0; i < numRows; i++) {
            this.generateNewRow();
        }
        this.initializeEvents();
    },
    generateNewRow() {
        const _this = this;
        const tr = $("<tr></tr>");

        // primary key
        tr.append(
            `<td class="text-center">
                <div class="ui checkbox"><input type="checkbox" name="PK" id="chk-primary-key"></div>
            </td>`
        );

        // unique
        tr.append(
            `<td class="text-center">
                <div class="ui checkbox"><input type="checkbox" name="UQ" id="chk-unique"></div>
            </td>`
        );

        // column name
        tr.append(
            `<td>
                <div class="ui input fluid"><input type="text" placeholder="Escribe algo..." id="txt-column-name"></div>
            </td>`
        );

        // datatype
        const dataTypesOption = this.dataTypes.map(x => `<option value="${x.dataTypeId}">${x.dataTypeName}</option>`)
        const dataTypeLenRegex = { "mask": "(9{1,},9{1,})", "greedy": false, "placeholder": " ", "showMaskOnHover": false };
        tr.append(
            `<td>
                <select class="ui dropdown fluid" id="cbb-data-type">
                    <option value="" selected>Selecciona</option>
                    ${dataTypesOption.join("")}
                </select>
                <hr>
                <div class="ui labeled input fluid">
                    <input id="txt-data-type-len" type="text" placeholder="(len1,len2)" class="text-center" data-input-mask='${JSON.stringify(dataTypeLenRegex)}'>
                </div>
            </td>`
        );

        // is nullable
        tr.append(
            `<td class="text-center">
                <div class="ui checkbox"><input type="checkbox" id="chk-is-nullable" checked></div>
            </td>`
        )

        // is auto increment
        tr.append(
            `<td class="text-center">
                <div class="ui checkbox"><input type="checkbox" id="chk-is-autoincrement"></div>
            </td>`
        )

        // seed
        const seedRegex = { "mask": "(9{1,},9{1,})", "greedy": false, "placeholder": " ", "showMaskOnHover": false };
        tr.append(
            `<td>
                <div class="ui input fluid disabled">
                    <input id="txt-autoincrement-seed" type="text" placeholder="(<semilla>,<salto>)" class="text-center" data-input-mask='${JSON.stringify(seedRegex)}'>
                </div>
            </td>`
        )

        // foreign key table
        const tableOption = this.tables.map(x => `<option value="${x.objectId}">[${x.schema}].[${x.objectName}]</option>`)
        tr.append(
            `<td>
                <select class="ui dropdown fluid" id="cbb-fk-table">
                    <option value="" selected>Selecciona</option>
                    ${tableOption.join("")}
                </select>
            </td>`
        )

        // foreign key column
        tr.append(
            `<td>
                <select class="ui dropdown fluid" id="cbb-fk-column">
                    <option value="" selected>Selecciona una Tabla</option>
                </select>
            </td>`
        )

        tr.find("#chk-is-autoincrement").on("change", function () {

            tr.find("#txt-autoincrement-seed").parent().addClass("disabled");
            if ($(this).is(":checked")) {
                tr.find("#txt-autoincrement-seed").parent().removeClass("disabled");
            }
        });

        tr.find("#chk-primary-key").on("change", function () {
            tr.find(`[name="UQ"]`).prop("checked", false);

            tr.find("#cbb-fk-table").removeClass("disabled");
            tr.find("#cbb-fk-column").removeClass("disabled");

            if ($(this).is(":checked")) {
                tr.find("#cbb-fk-table").addClass("disabled");
                tr.find("#cbb-fk-column").addClass("disabled");

                _this.grdColumnsBody.find("[name='PK']").not(this).prop("checked", false).trigger("change");
            }
        });

        tr.find("#chk-unique").on("change", function () {
            if (tr.find("#chk-primary-key").is(":checked")) {
                $(this).prop("checked", false);
            }
        });

        tr.find("#cbb-fk-table").on("change", function () {
            const tableId = $(this).val();
            const columns = _this.tablesColumns.filter(x => x.parentOf == tableId);
            const option = columns.map(x => `<option value="${x.objectId}">[${x.objectName}]</option>`)

            tr.find("#cbb-fk-column").html(`<option value="" selected>Selecciona</option>`);
            tr.find("#cbb-fk-column").append(option);
        });

        this.grdColumnsBody.append(tr);
    },
    async fillDataTypes() {
        const _this = this;
        await $.ajax({
            url: "/serveObjects/dataTypes",
            method: "POST",
            dataType: "JSON",
            success(_response) {
                _this.dataTypes = _response.data ?? [];
            },
            error(_response) {
                _this.dataTypes = [];
            },
        });
    },
    async fillTableObjects() {
        const _this = this;
        await $.ajax({
            url: "/serveObjects/list",
            method: "POST",
            dataType: "JSON",
            success(_response) {
                const objects = _response.data ?? [];
                _this.tables = objects.filter(x => x.objectTypeId == 1004)
                _this.tablesColumns = objects.filter(x => x.objectTypeId == 1009);
            },
            error(_response) {
                _this.tables = [];
            },
        });
    },
    async constructTable() {
        $("#grd-action-buttons .ui.button").removeClass("disabled");
        const columns = [];
        let columnsValid = true;
        $(this.grdColumnsBody).find("tr").each(function () {
            const tr = $(this);

            tr.removeClass(["red", "green"]);

            const columnName = tr.find("#txt-column-name").val();
            if (isEmpty(columnName)) {
                return true; // pasa a al siguiente fila
            }

            let dataType = tr.find("#cbb-data-type").val();
            let dataTypeLen = tr.find("#txt-data-type-len").val().replaceAll("(", "").replaceAll(")", "").split(",");

            if (isEmpty(dataType)) {
                columnsValid = false;
                tr.addClass("red");
                return false;
            }
            let isAutoIncrement = tr.find("#chk-is-autoincrement").is(":checked") ? "Y" : "N";
            let seed = tr.find("#txt-autoincrement-seed").val().replaceAll("(", "").replaceAll(")", "").split(",");

            let fk_table = tr.find("#cbb-fk-table").val();
            let fk_column = tr.find("#cbb-fk-column").val();

            let index = null;
            if (tr.find("#chk-primary-key").is(":checked")) index = "PRIMARY KEY";
            else if (tr.find("#chk-unique").is(":checked")) index = "UNIQUE";
            else if (!isEmpty(fk_table) && !isEmpty(fk_column)) index = "FOREIGN KEY";

            let isNullable = tr.find("#chk-is-nullable").is(":checked") ? "Y" : "N";
            if (["PRIMARY KEY"].includes(index)) isNullable = "N";


            columns.push({
                "name": columnName,
                "dataType": dataType,
                "len1": isEmpty(dataTypeLen[0]) ? null : dataTypeLen[0],
                "len2": isEmpty(dataTypeLen[1]) ? null : dataTypeLen[1],
                "index": index,
                "isNullable": isNullable,
                "isAutoIncrement": isAutoIncrement,
                "seed": isEmpty(seed[0], 1),
                "lenIncrement": isEmpty(seed[1], 1),
                "fk_table_id": isEmpty(fk_table) ? null : fk_table,
                "fk_table_column": isEmpty(fk_column) ? null : fk_column
            });

            tr.addClass("green");
        });

        if (columnsValid) {
            const model = {
                "tableName": $("#txt-table-name").val(),
                "schema": $("#txt-schema-name").val(),
                "columns": columns
            }

            $("#grd-action-buttons .ui.button").addClass("disabled");
            const requestStatus = await this.createTable(model);
            if (!requestStatus) {
                $("#grd-action-buttons .ui.button").removeClass("disabled");
            }
        } else {
            SwalToast.fire({
                icon: "error",
                title: "Algunas filas contienen errores..."
            });
        }
    },
    async createTable(model) {
        const _this = this;

        let status = true;
        await $.ajax({
            url: "/table/create",
            method: "POST",
            dataType: "JSON",
            data: model,
            success(_response) {
                if (!_response.success) {
                    console.log(_response);
                    SwalToast.fire({
                        icon: "error",
                        title: _response.message
                    });
                    status = false;
                    return false;
                }
                console.log(_response);
                SwalToast.fire({
                    icon: "success",
                    title: _response.message
                });
            },
            error(_response) {
                status = false;
                console.log(_response);
                SwalToast.fire({
                    icon: "error",
                    title: _response.responseText
                });
            },
        });

        return status;
    }
}

$(function () {
    createTableModule.init();
})