import { request } from './practice-api';
import { createStudyView } from './study-view';

export async function setupStudy() {
    const root = document.querySelector('[data-study]');
    if (!root) return;
    const view = createStudyView(root);
    let state;
    let index = 0;
    let busy = true;

    const lock = (value) => { busy = value; view.lock(value, state, index); };
    const answered = (card) => Object.prototype.hasOwnProperty.call(state.answers, card.id);
    const show = () => {
        view.render(state, index);
        const current = state.cards[index];
        view.message(answered(current)
            ? `Penilaian tersimpan: ${state.answers[current.id] ? 'Sudah Hafal' : 'Belum Hafal'}. Anda dapat mengubahnya.`
            : 'Pikirkan jawabannya, lalu balik kartu.');
        view.lock(busy, state, index);
    };
    const navigate = (direction) => {
        if (busy) return;
        index = Math.max(0, Math.min(state.cards.length - 1, index + direction));
        show();
    };

    view.find('flip').addEventListener('click', () => { if (!busy) view.flip(); });
    view.find('previous').addEventListener('click', () => navigate(-1));
    view.find('next').addEventListener('click', () => navigate(1));
    root.querySelectorAll('[data-mark]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (busy || !view.isFlipped()) return;
            lock(true);
            view.message('Menyimpan penilaian…');
            try {
                state = await request(root.dataset.answerUrl, 'PUT', {
                    card_id: state.cards[index].id, mastered: button.dataset.mark === 'true',
                });
                const next = [...state.cards.slice(index + 1), ...state.cards.slice(0, index + 1)]
                    .find((card) => !answered(card));
                if (next) index = state.cards.findIndex((card) => card.id === next.id);
                show();
                view.message(next ? 'Penilaian tersimpan. Lanjut ke kartu berikutnya.'
                    : 'Semua kartu telah dijawab. Pilih Selesaikan Latihan untuk melihat hasil.');
            } catch (error) { view.message(error.message); }
            finally { lock(false); }
        });
    });

    view.find('shuffle').addEventListener('click', async () => {
        if (busy) return;
        lock(true);
        view.message('Mengacak urutan…');
        try {
            state = await request(root.dataset.shuffleUrl, 'POST');
            index = 0;
            show();
            view.message('Urutan diacak. Progres jawaban tetap tersimpan.');
        } catch (error) { view.message(error.message); }
        finally { lock(false); }
    });

    view.find('finish').addEventListener('submit', (event) => {
        if (busy || !state) { event.preventDefault(); return; }
        if (state.stats.unanswered && !window.confirm(`Masih ada ${state.stats.unanswered} kartu belum dijawab. Selesaikan latihan sekarang?`)) {
            event.preventDefault();
            return;
        }
        lock(true);
    });

    document.addEventListener('keydown', (event) => {
        if (busy || event.repeat || event.altKey || event.ctrlKey || event.metaKey) return;
        if (event.target.closest('input, textarea, select, [contenteditable="true"], [role="textbox"]')) return;
        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            navigate(event.key === 'ArrowLeft' ? -1 : 1);
        } else if (event.key === ' ' || event.key === 'Enter') {
            if (event.target.closest('button, a, summary')) return;
            event.preventDefault();
            view.flip();
        }
    });

    try {
        state = await request(root.dataset.stateUrl);
        if (state.finished) { window.location.assign(root.dataset.resultUrl); return; }
        const first = state.cards.findIndex((card) => !answered(card));
        index = first < 0 ? 0 : first;
        busy = false;
        show();
    } catch (error) {
        view.message(`${error.message} Muat ulang halaman untuk mencoba kembali.`);
    }
}
