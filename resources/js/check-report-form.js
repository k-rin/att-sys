$('form').submit(() => {
    const startDate = $('#start_date').val();
    const startTime = $('#start_time').val();
    const endDate = $('#end_date').val();
    const endTime = $('#end_time').val();
    const type = $('#type').val();
    const reason = $('#reason').val();
    if (startDate == '') {
        $('#modal-body').html('請輸入開始的日期。');
        $('#alertModal').modal('show');
        return false;
    }
    if (endDate == '') {
        $('#modal-body').html('請輸入結束的日期。');
        $('#alertModal').modal('show');
        return false;
    }
    const startAt = Date.parse(startDate + ' ' + startTime + ':00:00');
    const endAt = Date.parse(endDate + ' ' + endTime + ':00:00');
    if (startAt >= endAt) {
        $('#modal-body').html('請輸入正確的日期。');
        $('#alertModal').modal('show');
        return false;
    }
    if (type != 1 && reason == '') {
        $('#modal-body').html('特別休假以外請輸入理由。');
        $('#alertModal').modal('show');
        return false;
    }
    return true;
});
