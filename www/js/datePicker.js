$(document).ready(function () {
    startDatePicker();
});

function startDatePicker() {
    $('input[data-dateinput-type]').dateinput({
        datetime: {
            dateFormat: 'd.m.yy',
            timeFormat: 'H:mm',
            singleDatePicker: true,
            showDropdowns: true,
            options: { // options for type=datetime
                changeYear: true,
            }
        },
        'datetime-local': {
            dateFormat: 'd.m.yy',
            timeFormat: 'H:mm'
        },
        date: {
            dateFormat: 'd.m.yy'
        },
        month: {
            dateFormat: 'MM yy'
        },
        week: {
            dateFormat: "w. 'week of' yy"
        },
        time: {
            timeFormat: 'H:mm:ss'
        },
        options: { // options for type=datetime
            closeText: "Zavřít",
            currentText: "Aktuální",
            prevText: 'Předchozí',
            nextText: 'Další',
            monthNames: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
            monthNamesShort: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
            dayNames: ['Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota'],
            dayNamesShort: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So',],
            dayNamesMin: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
            changeMonth: true,
            changeYear: true,
            //defaultDate: new Date(new Date().setFullYear(new Date().getFullYear() - 20)),

        },
    });
    $('input[data-datepicker]').datepicker({
        format: "dd.mm.yyyy"
    });
    $('input[data-datetimepicker]').datetimepicker({
        format: "dd.mm.yyyy H:mm"
    });
}