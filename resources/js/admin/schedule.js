$(function () {
    if (!isTemplate) {
        let weekPicker = new WeekPicker(selectedDate);
        weekPicker.init({
            startDate: startDate,
            endDate: endDate
        });
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
});
