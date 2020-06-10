$(function () {
    if (!isTemplate) {
        initDatepicker();
    }

    $('.driver-select')
        .on('change', driverSelectOnChange)
        .trigger('change');

    function driverSelectOnChange(event) {
        let $this = $(this);
        let gapId = $this.data('gap-id');
        let $gapSelects = $('.driver-select[data-gap-id=' + gapId + ']').not(this);

        let newValue = $this.val();

        if (newValue) {
            $gapSelects
                .find('option[value=' + newValue + ']')
                .prop('disabled', true)
                .prop("selected", false)
        }

        let oldValue = $this.data('value');

        if (oldValue && newValue != oldValue) {
            $gapSelects.find('option[value=' + oldValue + ']')
                .prop('disabled', false)
        }

        $(this).data('value', newValue);
    }

    function initDatepicker() {
        let datePicker = $('#datepicker');

        datePicker.datepicker({
            weekStart: 1,
            calendarWeeks: true,
            autoclose: true,
            startDate: startDate,
            endDate: moment().add(1, 'weeks').endOf('isoWeek').format('MM/DD/YYYY'),
            todayHighlight: false,
        });

        datePicker.datepicker('setDate', selectedDate);

        setDatepickerEvents(datePicker);

        datePicker.trigger('changeDate', true);
    }

    function setDatepickerEvents(datePicker) {
        datePicker.on('changeDate', function(e, skipSubmit) {
            let date = getMomentDate(datePicker);
            setDateRange(date);

            if (typeof skipSubmit === 'undefined') {
                let $form = $('#week-form');
                let currentYear = $form.find('input[name=year]').val() || date.year();
                let currentNumber = $form.find('input[name=number]').val() || date.isoWeek();
                $form.find('input[name=year]').val(date.year());
                $form.find('input[name=number]').val(date.isoWeek());

                // TODO submit only if week changed
                // if (currentYear != date.year() || currentNumber != date.isoWeek()) {
                    $form.submit();
                // }
            }
        });

        datePicker.on('hide', function(e) {
            let date = getMomentDate(datePicker);
            setDateRange(date);
        });
    }

    function setDateRange(date) {
        let firstDate = date.day(1).format("MM/DD/YYYY");
        let lastDate = date.day(7).format("MM/DD/YYYY");
        setTimeout(function () {
            $("#datepicker").val(firstDate + " - " + lastDate)
        }, 1);
    }

    function getMomentDate(datePicker) {
        return moment(
            datePicker.datepicker('getDate'),
            "MM/DD/YYYY"
        )
    }
});
