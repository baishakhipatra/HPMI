function toastFire(type = 'error', title) {
    Swal.fire({
    toast: true,
    position: 'bottom',
    timer: 3000,
    icon: type,
    title: title,
    showConfirmButton: false,
    background: type === 'error' ? '#dc3545' : '#d1e7dd', // red for error
    color: type === 'error' ? '#ffffff' : '#0f5132', 
    });
}

// enable tooltip everywhere
//$('[data-toggle="tooltip"]').tooltip();
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
});

// status toggle
function statusToggle(route) {
    $.ajax({
    url: route,
    success: function(resp) {
        if (resp.status == 200) {
        toastFire('success', resp.message);
        } else {
        toastFire('error', resp.message);
        }
    }
    });
}