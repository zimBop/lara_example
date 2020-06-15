class WeekPicker {
    constructor(selectedDate) {
        this.$datePicker = $('#datepicker');
        this.selectedDate = selectedDate
    }

    init(options = {}) {
        let defaults = {
            weekStart: 1,
            calendarWeeks: true,
            autoclose: true,
            endDate: moment().add(1, 'weeks').endOf('isoWeek').format('MM/DD/YYYY'),
            todayHighlight: false,
        };

        this.$datePicker.datepicker(Object.assign({}, defaults, options));

        this.$datePicker.datepicker('setDate', this.selectedDate);

        this.setEvents();

        this.$datePicker.trigger('changeDate', true);
    }

    setEvents() {
        let self = this;

        this.$datePicker.on('changeDate', function(e, skipSubmit) {
            let date = self.getMomentDate();
            self.setDateRange(date);

            if (typeof skipSubmit === 'undefined') {
                let $form = self.$datePicker.closest("form");
                let currentYear = $form.find('input[name=year]').val() || date.year();
                let currentNumber = $form.find('input[name=number]').val() || date.isoWeek();
                $form.find('input[name=year]').val(date.year());
                $form.find('input[name=number]').val(date.isoWeek());

                // TODO submit only if week changed
                // if (currentYear != date.year() || currentNumber != date.isoWeek()) {
                if ($form.data('submit-on-week-change')) {
                    $form.submit();
                }
                // }
            }
        });

        this.$datePicker.on('hide', function(e) {
            let date = self.getMomentDate();
            self.setDateRange(date);
        });
    }

    setDateRange(date) {
        let self = this;
        let firstDate = date.day(1).format("MM/DD/YYYY");
        let lastDate = date.day(7).format("MM/DD/YYYY");
        setTimeout(function () {
            self.$datePicker.val(firstDate + " - " + lastDate)
        }, 1);
    }

    getMomentDate() {
        return moment(
            this.$datePicker.datepicker('getDate'),
            "MM/DD/YYYY"
        )
    }
}
