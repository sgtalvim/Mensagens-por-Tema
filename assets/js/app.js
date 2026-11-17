document.addEventListener('DOMContentLoaded', () => {
    // Confirmação antes de excluir registros.
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    // Contador de caracteres da mensagem.
    document.querySelectorAll('textarea[data-counter-target]').forEach((textarea) => {
        const contador = document.getElementById(textarea.dataset.counterTarget);
        if (!contador) {
            return;
        }

        const atualizar = () => {
            contador.textContent = textarea.value.length;
        };

        textarea.addEventListener('input', atualizar);
        atualizar();
    });

    // Atalho: Ctrl/Cmd + Enter envia o formulário.
    document.querySelectorAll('textarea').forEach((textarea) => {
        textarea.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                textarea.form?.requestSubmit();
            }
        });
    });
});
