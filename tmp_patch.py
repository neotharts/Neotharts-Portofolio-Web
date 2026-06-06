from pathlib import Path
path = Path(r'c:\laragon\www\neotharts-portofolio\resources\views\admin\invoices\index.blade.php')
text = path.read_text(encoding='utf-8')
old = '''    function updateColumnCounts() {
        const statuses = ['unpaid', 'sketch', 'progress', 'finishing', 'done'];
        statuses.forEach(status => {
            const count = document.querySelectorAll(`.column-cards[data-status="${status}"] .client-card`).length;
            const countEl = document.querySelector(`.kanban-board[data-status="${status}"] .column-count`);
            if (countEl) countEl.textContent = count;
        });
    }
'''
new = '''    function updateColumnCounts() {
        const statuses = ['unpaid', 'sketch', 'progress', 'finishing', 'done'];
        statuses.forEach(status => {
            const columnCards = document.querySelector(`.column-cards[data-status="${status}"]`);
            const cardCount = columnCards ? columnCards.querySelectorAll('.client-card').length : 0;
            const countEl = document.querySelector(`.kanban-board[data-status="${status}"] .column-count`);
            if (countEl) countEl.textContent = cardCount;

            if (!columnCards) {
                return;
            }

            const emptyState = columnCards.querySelector('.empty-state');
            if (cardCount === 0) {
                if (!emptyState) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'empty-state';
                    placeholder.innerHTML = '<span class="material-icons-outlined">inbox</span><p>Belum ada invoice</p>';
                    columnCards.appendChild(placeholder);
                }
            } else if (emptyState) {
                emptyState.remove();
            }
        });
    }
'''
if old not in text:
    raise SystemExit('Old text not found')
path.write_text(text.replace(old, new), encoding='utf-8')
print('patched')
