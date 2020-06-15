$(function () {
    let weekPicker = new WeekPicker(selectedDate);
    weekPicker.init({
        startDate: startDate,
        endDate: endDate
    });
});
