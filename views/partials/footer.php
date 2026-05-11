<!-- Partial de rodapé compartilhado por todas as views. Fecha o conteúdo principal e carrega os scripts do Bootstrap 5. -->

</div><!-- fecha #main-content -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Inicializa dropdowns com strategy:'fixed' para não serem cortados por containers com overflow (table-responsive)
document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
    new bootstrap.Dropdown(el, { popperConfig: { strategy: 'fixed' } });
});
</script>

<script>
// Ordenação de tabelas por coluna — aplica em toda tabela com a classe "sortable-table"
document.querySelectorAll('table.sortable-table').forEach(tabela => {
    const cabecalhos = tabela.querySelectorAll('thead th');

    cabecalhos.forEach((th, coluna) => {
        th.style.cursor = 'pointer';
        th.style.userSelect = 'none';
        th.dataset.ordem = ''; // '' = neutro, 'asc', 'desc'

        // Adiciona ícone de ordenação ao lado do texto
        const icone = document.createElement('span');
        icone.className = 'sort-icon ms-1 text-white-50';
        icone.innerHTML = '<i class="bi bi-arrow-down-up" style="font-size:11px;"></i>';
        th.appendChild(icone);

        th.addEventListener('click', () => {
            const ordemAtual = th.dataset.ordem;
            const novaOrdem  = ordemAtual === 'asc' ? 'desc' : 'asc';

            // Reseta todos os cabeçalhos
            cabecalhos.forEach(outra => {
                outra.dataset.ordem = '';
                const ic = outra.querySelector('.sort-icon');
                if (ic) ic.innerHTML = '<i class="bi bi-arrow-down-up" style="font-size:11px;opacity:0.4;"></i>';
            });

            // Marca o cabeçalho ativo
            th.dataset.ordem = novaOrdem;
            icone.innerHTML = novaOrdem === 'asc'
                ? '<i class="bi bi-arrow-up" style="font-size:11px;"></i>'
                : '<i class="bi bi-arrow-down" style="font-size:11px;"></i>';

            // Ordena as linhas
            const tbody  = tabela.querySelector('tbody');
            const linhas = Array.from(tbody.querySelectorAll('tr'));

            linhas.sort((a, b) => {
                const celulaA = a.querySelectorAll('td')[coluna];
                const celulaB = b.querySelectorAll('td')[coluna];

                if (!celulaA || !celulaB) return 0;

                const textoA = celulaA.innerText.trim();
                const textoB = celulaB.innerText.trim();

                // Tenta comparar como data (dd/mm/aaaa ou aaaa-mm-dd)
                const dataA = parsarData(textoA);
                const dataB = parsarData(textoB);
                if (dataA && dataB) {
                    return novaOrdem === 'asc' ? dataA - dataB : dataB - dataA;
                }

                // Tenta comparar como número
                const numA = parseFloat(textoA.replace(/\./g, '').replace(',', '.'));
                const numB = parseFloat(textoB.replace(/\./g, '').replace(',', '.'));
                if (!isNaN(numA) && !isNaN(numB)) {
                    return novaOrdem === 'asc' ? numA - numB : numB - numA;
                }

                // Compara como texto (suporte a acentos em português)
                return novaOrdem === 'asc'
                    ? textoA.localeCompare(textoB, 'pt-BR')
                    : textoB.localeCompare(textoA, 'pt-BR');
            });

            linhas.forEach(linha => tbody.appendChild(linha));
        });
    });
});

function parsarData(texto) {
    // Aceita dd/mm/aaaa e dd/mm/aaaa hh:mm
    const br = texto.match(/^(\d{2})\/(\d{2})\/(\d{4})/);
    if (br) return new Date(`${br[3]}-${br[2]}-${br[1]}`);
    // Aceita aaaa-mm-dd
    const iso = texto.match(/^\d{4}-\d{2}-\d{2}$/);
    if (iso) return new Date(texto);
    return null;
}
</script>

</body>
</html>
