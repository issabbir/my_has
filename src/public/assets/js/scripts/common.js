$(".alert").fadeTo(5000, 5000).slideUp(500, function(){
    $(".alert").slideUp(500);
});

/** Employees and Employee **/
function employees(idSelector, employeesFilterUrl, selectedEmployeeUrl, callback, excludes) {
    $(idSelector).select2({
        placeholder: "Select one",
        allowClear: false,
        width: '100%',
        ajax: {
            url: APP_URL + employeesFilterUrl,
            data: function (params) {
                var query = {
                    term: params.term,
                    exclude: excludes,
                }

                return query;
            },
            dataType: 'json',
            processResults: function (data) {
                var formattedResults = $.map(data, function (obj, idx) {
                    obj.id = obj.emp_code;
                    obj.text = obj.emp_code;
                    return obj;
                });
                return {
                    results: formattedResults,
                };
            }
        }
    });

    if (
        ($(idSelector).attr('data-emp-code') !== undefined) && ($(idSelector).attr('data-emp-code') !== null) && ($(idSelector).attr('data-emp-code') !== '')
    ) {
        selectDefaultEmployee($(idSelector), selectedEmployeeUrl, $(idSelector).attr('data-emp-code'));
    }

    $(idSelector).on('select2:select', function (e) {
        var selectedOptionObj = e.params.data;
        if (selectedOptionObj.emp_code) {
            $.ajax({
                type: "GET",
                url: APP_URL + selectedEmployeeUrl + selectedOptionObj.emp_code,
                success: function (data) {
                    callback(data);
                },
                error: function (data) {
                    alert('error');
                }
            });
        }
    });
}

function selectDefaultEmployee(selector, selectedEmployeeUrl, empCode)
{
    $.ajax({
        type: 'GET',
        url: APP_URL + selectedEmployeeUrl  + empCode,
    }).then(function (data) {
        // create the option and append to Select2
        var option = new Option(data.emp_code, data.emp_code, true, true);
        selector.append(option).trigger('change.select2');

        // manually trigger the `select2:select` event
        selector.trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
    });
}
/** Employees and Employee **/

function ajaxParams(params)
{
    if(params.term) {
        if (params.term.trim().length  < 1) {
            return false;
        }
    } else {
        return false;
    }

    return params;
}

function employeeOptions(data)
{
    var formattedResults = $.map(data, function(obj, idx) {
        obj.id = obj.emp_id;
        obj.text = obj.emp_code + ': ' + obj.emp_name;
        return obj;
    });
    return {
        results: formattedResults,
    };
}

function select(elem, url, ajaxParamsCallBack, processResultsCallBack)
{
    $(elem).select2({
        placeholder: "Select",
        allowClear: true,
        width: "100%",
        ajax: {
            url: APP_URL+url,
            data: ajaxParamsCallBack,
            dataType: 'json',
            processResults: processResultsCallBack
        }
    });
}

$(".dynamicModal").on("click", function () {

    var news_id=this.getAttribute('news_id');
    $.ajax(
        {
            type: 'GET',
            url: '/get-top-news',
            data: {news_id:news_id},
            dataType: "json",
            success: function (data) {
                $("#dynamicNewsModalContent").html(data.newsView);
                $('#dynamicNewsModal').modal('show');
            }
        }
    );

});

//TODO:toCheckMaxMinLength
function toCheckMaxMinLength(param){
    let jqueryElem =  $(param);
    let paramVal =   jqueryElem.val().length;
    let minLength =  jqueryElem.attr('minLength');
    let maxLength =  jqueryElem.attr('maxLength');

    //var filteredValue = jqueryElem.val().replace(/[^0-9]+/g, "");
    //jqueryElem.val(filteredValue);

    if ( paramVal > maxLength ) {
        jqueryElem.val(jqueryElem.val().slice(0, maxLength));
        alert("Please enter "+maxLength+" Characters, extra Characters already removed");
        $(param).focus();
        return false;
    }

    if(paramVal < minLength) {
        if(minLength == maxLength){
            $(param).nextAll('.text-danger').text("Please enter total "+maxLength+" characters");
        }else{
            $(param).nextAll('.text-danger').text("Please enter between "+minLength+" TO "+maxLength+" characters");
        }
        $(param).focus();
        return false;
    }
    return true;
}

//TODO:toCheckMaxMinLength
function toCheckMaxMinValue(param){
    let jqueryElem =  $(param);
    let paramVal =    Number($(param).val());
    let minimumValue =  Number(jqueryElem.attr('min'));
    let maximumValue =  Number(jqueryElem.attr('max'));
    let maxlimit = Number(maximumValue.length);

    if ( paramVal > maximumValue ) {
        let sliceData = Number(jqueryElem.val().slice(0, maxlimit));
        //console.log(sliceData);
        if(sliceData >maximumValue){
            $(param).val('');
            $(param).nextAll('.text-danger').text("Please enter value between "+minimumValue+" TO "+maximumValue+" ");
        }else{
            $(param).val(sliceData);
            $(param).nextAll('.text-danger').text("Please enter maximum value "+maximumValue+" , extra value already removed");
        }
        $(param).focus();
        return false;
    }else if(paramVal < minimumValue) {
        if(minimumValue == maximumValue){
            $(param).nextAll('.text-danger').text("Please enter value "+maximumValue+" ");
        }else{
            $(param).nextAll('.text-danger').text("Please enter value between "+minimumValue+" TO "+maximumValue+" ");
        }
        $(param).focus();
        return false;
    }else{
        $(param).nextAll('.text-danger').text("");
    }
    return true;
}


function dateRangePicker(Elem1, Elem2){
    let minElem = $(Elem1);
    let maxElem = $(Elem2);

    minElem.datetimepicker({
        useCurrent: false,
        maxDate: new Date(),
        format: 'DD-MM-YYYY',
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });
    maxElem.datetimepicker({
        useCurrent: true,
        maxDate: new Date(),
        format: 'DD-MM-YYYY',
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });

    minElem.on("change.datetimepicker", function (e) {
        maxElem.datetimepicker('minDate', e.date);
    });
    maxElem.on("change.datetimepicker", function (e) {
        minElem.datetimepicker('maxDate', e.date);
    });

    let preDefinedDateMin = minElem.attr('data-predefined-date');
    let preDefinedDateMax = maxElem.attr('data-predefined-date');

    if (preDefinedDateMin) {
        let preDefinedDateMomentFormat = moment(preDefinedDateMin, "YYYY-MM-DD").format("DD-MM-YYYY");
        minElem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }

    if (preDefinedDateMax) {
        let preDefinedDateMomentFormat = moment(preDefinedDateMax, "YYYY-MM-DD").format("DD-MM-YYYY");
        maxElem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }

}


function maxMinDatePickerUsingDiv(divSelector,minDate='',maxDate='',dateFormat = 'YYYY-MM-DD') { // divSelector is the targeted parent div of date input field

    //console.log(divSelector+' # '+minDate+' # '+maxDate+' # '+dateFormat);

    var elem = $(divSelector);
    if(!minDate && maxDate){
        elem.datetimepicker({
            format: dateFormat,
            ignoreReadonly: true,
            maxDate: moment(maxDate,"YYYY-MM-DD"),
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            },
            icons: {
                time: 'bx bx-time',
                date: 'bx bxs-calendar',
                up: 'bx bx-up-arrow-alt',
                down: 'bx bx-down-arrow-alt',
                previous: 'bx bx-chevron-left',
                next: 'bx bx-chevron-right',
                today: 'bx bxs-calendar-check',
                clear: 'bx bx-trash',
                close: 'bx bx-window-close'
            }
        });

        let maxDateL = moment(maxDate, "YYYY-MM-DD").format("YYYY-MM-DD");
        elem.datetimepicker('maxDate', maxDateL);

    }else if(minDate && !maxDate){
        elem.datetimepicker({
            format: dateFormat,
            ignoreReadonly: true,
            minDate: moment(minDate,"YYYY-MM-DD"),
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            },
            icons: {
                time: 'bx bx-time',
                date: 'bx bxs-calendar',
                up: 'bx bx-up-arrow-alt',
                down: 'bx bx-down-arrow-alt',
                previous: 'bx bx-chevron-left',
                next: 'bx bx-chevron-right',
                today: 'bx bxs-calendar-check',
                clear: 'bx bx-trash',
                close: 'bx bx-window-close'
            }
        });

        let minDateL = moment(minDate, "YYYY-MM-DD").format("YYYY-MM-DD");
        elem.datetimepicker('minDate', minDateL);

    }else{
        elem.datetimepicker({
            format: dateFormat,
            ignoreReadonly: true,
            //startDate: moment(minDate,"YYYY-MM-DD"),
            //endDate: moment(maxDate,"YYYY-MM-DD"),
            minDate: moment(minDate,"YYYY-MM-DD"),
            maxDate: moment(maxDate,"YYYY-MM-DD"),
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            },
            icons: {
                time: 'bx bx-time',
                date: 'bx bxs-calendar',
                up: 'bx bx-up-arrow-alt',
                down: 'bx bx-down-arrow-alt',
                previous: 'bx bx-chevron-left',
                next: 'bx bx-chevron-right',
                today: 'bx bxs-calendar-check',
                clear: 'bx bx-trash',
                close: 'bx bx-window-close'
            }
        });

        let minDateL = moment(minDate, "YYYY-MM-DD").format("YYYY-MM-DD");
        let maxDateL = moment(maxDate, "YYYY-MM-DD").format("YYYY-MM-DD");
        elem.datetimepicker('minDate', minDateL);
        elem.datetimepicker('maxDate', maxDateL);
    }
}
