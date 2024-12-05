const queryManager = {
    editor: null,
    editorId: "txt-code-editor",
    editorTheme: "ace/theme/sqlserver",
    editorMode: "ace/mode/sql",
    tabs: {
        log: "frm-output-log",
        fetching: "frm-fetching-rows"
    },
    logCount: 0,
    init() {
        this.editor = ace.edit(this.editorId);
        this.editor.setTheme(this.editorTheme);
        this.editor.session.setMode(this.editorMode);

        this.initializeEvents();
    },
    initializeEvents() {
        const _this = this;

        $(".item[data-tab]").tab();

        $("#btn-execute-code").on("click", async function () {
            const stringCode = _this.editor.getValue();
            await _this.executeCode(stringCode);
        });

        $("#btn-clear-editor").on("click", function () {
            _this.editor.setValue(""); // Establece el contenido vacío
            _this.editor.clearSelection();
        });

        $("#btn-clear-log").on("click", function () {
            $(`.tab[data-tab="${_this.tabs.log}"] #tbl-output-log tbody`).empty();
            $(`.tab[data-tab="${_this.tabs.fetching}"]`).empty();
            $(`.item[data-tab="${_this.tabs.fetching}"]`).html(`Resultados`);
        });

        $(".btn-generate-command").on("click", function () {
            const action = $(this).attr("data-action");
            const tableId = $("#cbb-tables").val();

            if (tableId != 0) {
                const tableName = $("#cbb-tables option:selected").attr("data-table");
                _this.generateCommand(tableId, tableName, action);
            }
        });
    },
    async executeCode(stringCode) {
        const _this = this;

        await $.ajax({
            url: "/executeQuery",
            method: "POST",
            dataType: "JSON",
            data: { sqlQuery: stringCode },
            success(_response) {
                if (!_response.success) {
                    SwalToast.fire({
                        icon: "error",
                        title: _response.Message
                    });
                    return;
                }

                const items = _response.data ?? [];
                items.forEach(log => {
                    _this.addLog({
                        timestamp: log.timeStamp,
                        status: log.status,
                        command: log.queryString,
                        message: log.status ? `${log.rowsAfected} fila(s) afectadas.` : log.error,
                        duration: log.executionTime
                    });
                });

                _this.fetchQueryResults(
                    items.filter(x => x.status && x.hasReturnData)
                );

            },
            error(_response) {
                _this.addLog({
                    timestamp: moment().format('HH:mm:ss'),
                    status: false,
                    command: stringCode,
                    message: "Internal Server Error: " + _response.responseText,
                    duration: 0
                });

                _this.fetchQueryResults();
            }
        });
    },
    addLog({ timestamp, status, command, message, duration }) {

        this.logCount++;
        let icon = status ? "green checkmark" : "red times";
        $(`.tab[data-tab="${this.tabs.log}"] #tbl-output-log tbody`).append(
            `<tr>
                <td><i class="${icon} circular inverted icon"></i></td>
                <td>${timestamp}</td>
                <td title="${command}">${command}</td>
                <td title="${message}">${message}</td>
                <td>${duration} sec.</td>
            </tr>`
        );
    },
    fetchQueryResults(querysFetchs = []) {

        const container = $(`.tab[data-tab="${this.tabs.fetching}"]`);
        let actualQuery = 0;
        let totalQuerys = querysFetchs.length;

        $(`.item[data-tab="${this.tabs.fetching}"]`).html(`Resultados (${totalQuerys})`);
        container.empty();

        querysFetchs.forEach(queryFetch => {
            actualQuery++;
            queryFetch.headers.unshift("#");

            const tableId = `fetch-table-${actualQuery}`;
            const table = $(`<table class="ui overflowing long selectable stuck fixed single line celled tiny table fs-xx-small" id="${tableId}"></table>`);
            const tableColumns = queryFetch.headers.map(col => {
                const width = col == "#" ? "one wide" : "";
                return `<th class="${width}" title="${col}">${col}</th>`
            });
            table.append(
                `<thead>
                    <tr>${tableColumns}</tr>
                </thead>`
            );

            table.append("<tbody></tbody>");
            let rowNumber = 0;
            queryFetch.data.forEach(row => {
                rowNumber++;
                row["#"] = rowNumber;
                const tr = $("<tr></tr>");
                queryFetch.headers.forEach(col => {
                    let text = row[col] ?? `<span class='ui info text'>NULL</span>`;
                    tr.append(`<td title="${text}">${text}</td>`)
                });
                table.find("tbody").append(tr);
            });

            // ---------------------

            container.append(
                `<h4>Resultados de bloque de sentencia (${actualQuery} / ${totalQuerys})</h4>
                <button class="ui green icon button btn-export-query-results" title="Exportar a Excel" data-source="${tableId}"><i class="excel file icon"></i></button>`
            );
            container.append(table);
            container.append(`<div class="ui divider"></div>`);
        });

        container.find(".btn-export-query-results").on("click", function () {
            const sourceTableId = $(this).attr("data-source");
            exportTableToExcel(sourceTableId, `RESULTADOS CONSULTA SQL #${actualQuery}`);
        });
    },
    async generateCommand(tableId, tableName, action) {
        let columns = [];

        await $.ajax({
            url: `/table/${tableId}/columns`,
            method: "POST",
            dataType: "JSON",
            success(_response) {
                if (!_response.success) {
                    console.log(_response);
                    SwalToast.fire({
                        icon: "error",
                        title: _response.message
                    });
                    return;
                }
                columns = _response.data ?? [];
            },
            error(_response) {
                console.log(_response);
                SwalToast.fire({
                    icon: "error",
                    title: _response.responseText
                });
            }
        });

        if (columns.length == 0) {
            return;
        }

        const columnsName = columns.map(x => x.name);
        const primaryKey = columns.filter(x => x.index == "PRIMARY KEY")[0] ?? {};
        const primaryKeyName = primaryKey.name ?? "id";
        let sqlCommand = "";

        switch (action) {
            case "SELECT":
                sqlCommand += `SELECT ${columnsName.join(", ")}\nFROM ${tableName}`
                break;
            case "INSERT":
                sqlCommand += `INSERT INTO ${tableName}\n`
                sqlCommand += `\t(${columnsName.filter(x => ![primaryKeyName].includes(x)).join(", ")})\n`;
                sqlCommand += `VALUES\n\t('${columnsName.filter(x => ![primaryKeyName].includes(x)).join("', '")}')`
                break;
            case "UPDATE":
                let fields = columnsName.filter(x => ![primaryKeyName].includes(x)).map(x => `\t${x} = ''`);
                sqlCommand += `UPDATE ${tableName}\nSET\n`
                sqlCommand += fields.join("\n");
                sqlCommand += `\nWHERE ${primaryKeyName} = ''`
                break;
            case "DELETE":
                sqlCommand += `DELETE FROM ${tableName} WHERE ${primaryKeyName} = ''`;
                break;

        }

        if (sqlCommand != "") {
            sqlCommand += ";";
            this.editor.setValue(sqlCommand);
        }
    }
}

$(function () {
    queryManager.init();
});