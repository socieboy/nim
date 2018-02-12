
module.exports = class Alert {

    static error(message) {
        swal('', message, 'error')
    }

    static success(message) {
        swal('', message, 'success')
    }

    static info(message) {
        swal('', message, 'info')
    }

    static question(message) {
        swal('', message, 'question')
    }

    static warning(message) {
        swal('', message, 'warning')
    }
}