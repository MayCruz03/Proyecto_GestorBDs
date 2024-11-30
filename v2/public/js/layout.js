const menuBarComponent = {
    clases: {
        menuItem: "server-obj",
        menuItemList: "obj-list-items",
        toggleContentButton: "btn-toggle-content",
    },
    objectsKeys: {
        database: 1002,
        schema: 1003
    },
    serverObjectsTypes: [],
    serverObjectsList: [],
    serverObjectsMenuContainer: "#frm-server-objects-menu",
    createMenuItem(objTypeId, objId, objName, objIcon, listItems = []) {
        const _this = this;
        const template =
            `<div class="item ${this.clases.menuItem}" data-objTypeId="${objTypeId}" data-objId="${objId}" data-toggleContent="1">
                <i class="minus  teal icon ${this.clases.toggleContentButton}"></i>
                <i class="${objIcon} icon"></i>
                <div class="content">
                    <div class="header">${objName}</div>
                    <div class="list ${this.clases.menuItemList}"></div>
                </div>
            </div>`

        const item = $(template);
        listItems.forEach(listItem => { item.find(`.${this.clases.menuItemList}`).append(listItem) });

        item.find(`.${this.clases.toggleContentButton}`).on("click", function (e) {
            e.stopPropagation();
            const parent = $(this).parents(`.${_this.clases.menuItem}`).eq(0);
            const toggleStatus = parent.attr("data-toggleContent");

            $(this).removeClass(["minus", "plus"]);
            if (toggleStatus == 0) {
                $(parent).find(`.${_this.clases.menuItemList}`).eq(0).show();
                parent.attr("data-toggleContent", 1);
                $(this).addClass("minus");
            } else {
                $(parent).find(`.${_this.clases.menuItemList}`).eq(0).hide();
                parent.attr("data-toggleContent", 0);
                $(this).addClass("plus");
            }

        });

        return item;

    },
    async fillServerObjectsTypes() {
        const _this = this;
        await $.ajax({
            url: "/serveObjects/types",
            method: "POST",
            dataType: "JSON",
            success(_response) {
                _this.serverObjectsTypes = _response.data ?? [];
            },
            error(_response) {
                _this.serverObjectsTypes = [];
            },
        });
    },
    async fillServerObjectsList() {
        const _this = this;
        await $.ajax({
            url: "/serveObjects/list",
            method: "POST",
            dataType: "JSON",
            success(_response) {
                _this.serverObjectsList = _response.data ?? [];
            },
            error(_response) {
                _this.serverObjectsList = [];
            },
        });
    },
    initEvents() {
        $("#top-tool-bar, #frm-server-objects-menu").find(".ui.dropdown").dropdown();
        $("[data-content]").popup();

        $(".btn-toggle-menu[data-toggle]").on("click", function () {
            const status = parseInt($(this).attr("data-toggle"));
            $(`#frm-server-objects-menu [data-objtypeid="10002"] .obj-list-items .server-obj`).attr("data-togglecontent", status);
            $(`#frm-server-objects-menu [data-objtypeid="10002"] .obj-list-items .btn-toggle-content`).trigger("click");
        });
    },
    async init() {
        await this.fillServerObjectsTypes();
        await this.fillServerObjectsList();

        $(this.serverObjectsMenuContainer).empty();

        let databaseObjectsTypes = this.serverObjectsTypes.filter(x => x.objectParent == this.objectsKeys.database);
        let databases = this.serverObjectsList.filter(x => x.objectTypeId == this.objectsKeys.database);
        databases.forEach(db => {
            const menuItem = this.createMenuItem(db.objectTypeId, db.objectId, db.objectName, "database");
            $(this.serverObjectsMenuContainer).append(menuItem);

            const databaseRoot = `.${this.clases.menuItem}[data-objTypeId="${db.objectTypeId}"][data-objId="${db.objectId}"] .${this.clases.menuItemList}`;
            databaseObjectsTypes.forEach(obj => {

                let listItems = this.serverObjectsList.filter(x => x.objectTypeId == obj.objectId && x.parentOf == db.objectId);
                listItems = listItems.map(item => {
                    let objName = item.objectId == this.objectsKeys.schema ? item.schema : `${item.schema ?? ""}.${item.objectName}`
                    let template =
                        `<div class="item">
                            <i class="${obj.objectIcon} icon"></i>
                            <div class="content"><div class="description">${objName}</div></div>
                        </div>`;
                    return template;
                });
                if (listItems.length == 0) {
                    listItems.push(
                        `<div class="item">
                            <div class="content">
                                <div class="description"><i>Carpeta Vacia</i></div>
                            </div>
                        </div>`
                    )
                }
                const menuFolderItem = this.createMenuItem(obj.objectId, obj.objectId, obj.objectName, "yellow folder", listItems);
                $(databaseRoot).eq(0).append(menuFolderItem);
            });

            $(databaseRoot).find(`.${this.clases.toggleContentButton}`).trigger("click");
        });

        this.initEvents();
        // console.log(this);
    }
}

menuBarComponent.init();
