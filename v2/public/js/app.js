const SwalToast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

/*
ejemplo de uso
SwalToast.fire({
    icon: "success",
    title: "Signed in successfully"
});
*/

function isEmpty(value, replace = undefined) {
    // Check for null or undefined
    if (replace != undefined) {
        return isEmpty(value) ? replace : value;
    }

    if (value == null) return true;

    // Check for NaN
    if (typeof value === 'number' && isNaN(value)) return true;

    // Check for Infinity
    if (typeof value === 'number' && !isFinite(value)) return true;

    // Check for boolean false (retirar esta si quieres considerar `false` como válido)
    if (typeof value === 'boolean' && value === false) return true;

    // Check for string ""
    if (typeof value === 'string' && value.trim() === "") return true;

    // Check for empty arrays
    if (Array.isArray(value) && value.length === 0) return true;

    // Check for empty objects
    if (typeof value === 'object' && Object.keys(value).length === 0) return true;

    return false;
}

function exportTableToExcel(table_id, book_name, _sheet_name = "Sheet1") {

	// Obtiene los datos de las 5 tablas
	var sheet = XLSX.utils.table_to_sheet(document.getElementById(table_id));

	// Crea un libro de Excel y añade las hojas con los datos
	var libro = XLSX.utils.book_new();
	XLSX.utils.book_append_sheet(libro, sheet, _sheet_name);

	// Formatea la fecha en "yyyy-mm-dd"
	var fechaFormateada = moment().format('YYYY-MM-DD');

	XLSX.writeFile(libro, `${book_name} (${fechaFormateada}).xlsx`);
}
