$(".ckeditor").each(function(e) {
    var __editorName = this.id;
    CKEDITOR.replace(__editorName);
});

$.datetimepicker.setLocale("vi");
$("body").on("focus", ".date", function() {
    $(this).datetimepicker({
        timepicker: false,
        format: "d/m/Y",
        onGenerate: function(time, el) {
            if (!el[0].datepicker) el.datetimepicker("show");
            el[0].datepicker = true;
        }
    });
});

$("body").on("focus", ".datetime", function() {
    $(this).datetimepicker({
        format: "H:i d/m/Y",
        onGenerate: function(time, el) {
            if (!el[0].datepicker) el.datetimepicker("show");
            el[0].datepicker = true;
        }
    });
});

$("select.select2").select2();

function formatDate(date) {
    var dd = String(date.getDate()).padStart(2, "0");
    var mm = String(date.getMonth() + 1).padStart(2, "0"); //January is 0!
    var yyyy = date.getFullYear();

    today = mm + "/" + dd + "/" + yyyy;
    return today;
}

function reinitSelect2(selector) {
    setTimeout(() => {
        if (selector) {
            let data_select2 = $(selector).data("select2");
            let open = data_select2 ? data_select2.isOpen() : false;
            let modal_content = $(selector).closest(".modal-content");
            if (modal_content && modal_content.length) {
                $(selector).select2({
                    dropdownParent: modal_content
                });
            } else {
                $(selector).select2();
            }
            if (open) $(selector).select2("open");
        }
    });
}

$(document).on("focus", "select.select2-dynamic", function() {
    $(this)
        .select2()
        .select2("open");
});

$(document).on("focus", "select.select2-dynamic-modal", function() {
    let modal = $(this).closest(".modal-content");
    $(this)
        .select2({
            dropdownParent: modal
        })
        .select2("open");
});

$(document).on("show.bs.modal", ".modal", function() {
    let modal = $(this).find(".modal-content");
    $(this)
        .find(".select2-in-modal")
        .each(function() {
            $(this).select2({
                dropdownParent: modal
            });
        });
});

$("body").tooltip({
    selector: '[data-toggle="tooltip"]'
});

function parseNumberString(numberString) {
    if (!numberString && numberString != 0) return null;

    numberString = numberString.toString().replace(/[^0-9\.\-]/g, "");
    var result = Number(numberString);
    return result;
}

function roundNumber(number, decimlPlace = 1) {
    number = parseNumberString(number);
    return (
        Math.round(number * Math.pow(10, decimlPlace)) /
        Math.pow(10, decimlPlace)
    );
}

function roundDown(number, decimlPlace = 0) {
    number = parseNumberString(number);
    return (
        Math.floor(number * Math.pow(10, decimlPlace)) /
        Math.pow(10, decimlPlace)
    );
}

function randomString(length = 6) {
    let result = "";
    let characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(
            Math.floor(Math.random() * charactersLength)
        );
    }
    return result;
}

function arrayInclude(arr, e) {
    return arr && !!arr.find(val => val == e);
}

function buildFormData(formData, data, parentKey) {
    if (
        data &&
        typeof data === "object" &&
        !(data instanceof Date) &&
        !(data instanceof File)
    ) {
        Object.keys(data).forEach(key => {
            buildFormData(
                formData,
                data[key],
                parentKey ? `${parentKey}[${key}]` : key
            );
        });
    } else {
        const value = data == null ? "" : data;

        formData.append(parentKey, value);
    }
}

function jsonToFormData(data) {
    const formData = new FormData();

    buildFormData(formData, data);

    return formData;
}

function safeAppend(formData, key, value) {
    formData.append(key, value !== undefined && value !== null ? value : '');
}

function findType(arr, type) {
    return arr.find(val => val.id == type);
}

function convertToRoman(num) {
    let roman = {
        M: 1000,
        CM: 900,
        D: 500,
        CD: 400,
        C: 100,
        XC: 90,
        L: 50,
        XL: 40,
        X: 10,
        IX: 9,
        V: 5,
        IV: 4,
        I: 1
    };
    var str = "";

    for (var i of Object.keys(roman)) {
        var q = Math.floor(num / roman[i]);
        num -= q * roman[i];
        str += i.repeat(q);
    }

    return str;
}

function getFileName(path) {
    if (!path) return "";
    return path.split("/").pop();
}

function mergeSearch(object, context) {
    let id = context.nTable.id;
    $(`#${id}_wrapper .search-column [data-column]`).each(function() {
        if ($(this).data("column")) {
            let column = $(this).data("column");
            object[column] = $(this).val();
        }
    });
    object.startDate = $(`#${id}_wrapper .search-column .startDate`).val()
        ? moment(
              $(`#${id}_wrapper .search-column .startDate`).val(),
              "DD/MM/YYYY"
          ).format("YYYY-MM-DD")
        : "";
    object.endDate = $(`#${id}_wrapper .search-column .endDate`).val()
        ? moment(
              $(`#${id}_wrapper .search-column .endDate`).val(),
              "DD/MM/YYYY"
          ).format("YYYY-MM-DD")
        : "";
}

function resetSearch(id) {
    $(`#${id}_wrapper .search-column [data-column]`).each(function() {
        $(this).val("");
    });
    $(`#${id}_wrapper .search-column .startDate`).val("");
    $(`#${id}_wrapper .search-column .endDate`).val("");
    triggerSelect2();
}

$(document).ready(function() {
    $(".minus").click(function() {
        var $input = $(this)
            .parent()
            .find("input");
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });
    $(".plus").click(function() {
        var $input = $(this)
            .parent()
            .find("input");
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });
});
function saveSearch(table) {
    let object = {};
    let id = table.context[0].nTable.id;
    $(`#${id}_wrapper .search-column [data-column]`).each(function() {
        if ($(this).data("column")) {
            let column = $(this).data("column");
            object[column] = $(this).val();
        }
    });
    object.page = table.page();
    localStorage.setItem(
        id + "-" + window.location.href,
        JSON.stringify(object)
    );
}

function restoreSearch(table, object) {
    let id = table.context[0].nTable.id;
    Object.keys(object).forEach(key => {
        $(`#${id}_wrapper .search-column [data-column=${key}]`).val(
            object[key]
        );
    });
    triggerSelect2();
    if (object.page) table.page(object.page);
    setTimeout(() => table.draw("page"));
}

function getSavedSearch(table_id) {
    return JSON.parse(
        localStorage.getItem(table_id + "-" + window.location.href) || "{}"
    );
}

function datatableInitComplete(options, saved_search) {
    return function() {
        let column_data = options.column_data;
        let table = this.api();
        let table_id = this[0].id;
        let html = `<div class="row search-column mb-2">`;
        if (options.search_by_time) {
            html += `<div class="col-md-3 mb-1">
                <input type="text" class="form-control startDate date" placeholder="Từ ngày">
            </div>
            <div class="col-md-3 mb-1">
                <input type="text" class="form-control endDate date" placeholder="Đến ngày">
            </div>`;
        }

        let has_column_search = false;
        options.columns.forEach(col => {
            if (col.search_type) {
                has_column_search = true;
                if (col.search_type == "text") {
                    html += `<div class="col-md-4 mb-1">
                        <input type="text" class="form-control" data-column="${col.data}" placeholder="${col.placeholder}">
                    </div>`;
                } else if (col.search_type == "select") {
                    html += `<div class="col-md-2 mb-1">
                    <div class="form-group custom-group">
                    <label>${col.placeholder}</label>
                        <select class="form-control select2-dynamic custom-select" data-column="${col.data}">
                            <option value="">Chọn ${col.placeholder}</option>`;
                    column_data[col.data].forEach(function(el) {
                        if (typeof el == "string")
                            html +=
                                '<option value="' +
                                el +
                                '">' +
                                el +
                                "</option>";
                        else
                            html +=
                                '<option value="' +
                                el.id +
                                '">' +
                                el.name +
                                "</option>";
                    });
                    html += `</select></div></div>`;
                }
            }
        });

        if (options.search_by_time || has_column_search) {
            html += `<div class="col-md-3 mb-1">
                <button type="button" class="btn btn-success refresh-button" id = "refresh-button"><i class="fas fa-sync-alt mr-1"></i>Làm mới</button>
                <button type="button" class="btn btn-primary search-button"><i class="fas fa-search-plus mr-1"></i>Tìm kiếm</button>
            </div>`;
        }
        html += "</div>";
        $(`#${table_id}_wrapper`).prepend(html);
        $(`#${table_id}_wrapper .search-column`).on(
            "keyup change clear",
            "input, select",
            function() {
                table.draw();
            }
        );
        restoreSearch(table, saved_search);
    };
}

function initDatatable(id, options = {}) {
    let tableElement = $(`#${id}`);
    let saved_search = getSavedSearch(id);
    let datatable = tableElement.DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [],
        language: i18nDataTable,
        initComplete: datatableInitComplete(options, saved_search),
        drawCallback: function() {
            saveSearch(this.api());
        },
        ...options
    });

    $(`#${id}_wrapper`)
        .parent()
        .on("click", ".search-button, #search-button", function() {
            datatable.draw();
        });

    $(`#${id}_wrapper`)
        .parent()
        .on("click", ".refresh-button, #refresh-button", function() {
            resetSearch(id);
            datatable.draw();
        });

    return datatable;
}

function sendRequest(options = {}, scope = null) {
    let loading = $(
        `<div class = '_loaderContainer'><div class='_loaderOverlay'></div><div class ='_loaderContent'><div class='_loader'><span></span><span></span><span></span><span></span><span></span></div></div></div>`
    );
    $("body").append(loading);
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": CSRF_TOKEN
        },
        error: function() {
            toastr.error("Đã có lỗi xảy ra");
        },
        complete: function() {
            // $(loading).remove();
            if (scope) scope.$applyAsync();
        },
        ...options
    });
}

function sendRequestWithFile(options = {}, before = null) {
    if (before) before();
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": CSRF_TOKEN
        },
        processData: false,
        contentType: false,
        error: function() {
            toastr.error("Đã có lỗi xảy ra");
        },
        ...options
    });
}

function triggerSelect2() {
    setTimeout(() => {
        $(".select2").trigger("change");
        $(".select2-dynamic").trigger("change");
        $(".select2-in-modal").trigger("change");
        $(".select2-dynamic-modal").trigger("change");
    });
}

$(document).on("change", '.image-chooser input[type="file"]', function(e) {
    let fr = new FileReader();
    let target = $(this)
        .closest(".image-chooser")
        .find("img")[0];
    if (!target) return;
    fr.readAsDataURL(this.files[0]);
    fr.onload = function(e) {
        target.src = this.result;
    };
});

function htmlEncode(s) {
    var el = document.createElement("div");
    el.innerText = el.textContent = s;
    s = el.innerHTML;
    return s;
}

function getStatus(status, statuses) {
    let obj = statuses.find(val => val.id == status);
    if (!obj) return "";
    return `<span class="badge badge-${obj["type"]}">${obj["name"]}</span>`;
}

function getStatusText(status, statuses) {
    let obj = (statuses || []).find(val => val.id == status);
    if (!obj) return "";
    return obj["name"];
}

function dateGetter(
    date,
    input_format = "YYYY-MM-DD",
    output_format = "DD/MM/YYYY"
) {
    if (!date) return "";
    return moment(date, input_format).format(output_format);
}

function dateSetter(
    date,
    input_format = "DD/MM/YYYY",
    output_format = "YYYY-MM-DD"
) {
    if (!date) return null;
    return moment(date, input_format).format(output_format);
}

// function checkExists(inArr, value_name, value)
// {
//     for (i = 0; i < inArr.length; i++ )
//     {
//         if (inArr[i].value_name == Number(value))
//         {
//             return true;
//             alert('1111');
//             break;
//         }
//     }
// }

function getParam(param) {
    let urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}
