(() => {
    const flash = window.TECH_FOOD_FLASH || {};

    if (flash.success) {
        Swal.fire({
            icon: 'success',
            title: 'Sucesso',
            text: flash.success,
            background: '#1e1e1e',
            color: '#ffffff',
            confirmButtonColor: '#d4af37'
        });
    } else if (flash.error) {
        Swal.fire({
            icon: 'error',
            title: 'Atenção',
            text: flash.error,
            background: '#1e1e1e',
            color: '#ffffff',
            confirmButtonColor: '#d4af37'
        });
    }

    const updateClock = () => {
        const clock = document.getElementById('serviceClock');

        if (!clock) {
            return;
        }

        clock.textContent = new Intl.DateTimeFormat('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        }).format(new Date());
    };

    updateClock();
    window.setInterval(updateClock, 1000);

    $(document).on('click', '.sidebar-toggle', () => {
        document.body.classList.toggle('sidebar-open');
    });

    $(document).on('click', '.demo-user', function () {
        const email = this.getAttribute('data-demo-email');
        const password = this.getAttribute('data-demo-password');

        $('#email').val(email);
        $('#password').val(password).trigger('focus');
    });

    const editorElement = document.getElementById('quickNotesEditor');

    if (editorElement && typeof Quill !== 'undefined') {
        new Quill(editorElement, {
            theme: 'snow',
            modules: {
                toolbar: [['bold', 'italic', 'underline'], [{ list: 'bullet' }], ['clean']]
            }
        });
    }
})();