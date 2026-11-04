export function createStudyView(root) {
    const find = (name) => root.querySelector(`[data-${name}]`);
    const card = find('flip');
    let flipped = false;

    const flip = (value = !flipped) => {
        flipped = value;
        card.classList.toggle('is-flipped', flipped);
        card.setAttribute('aria-pressed', String(flipped));
        card.setAttribute('aria-label', flipped ? 'Balik kartu untuk melihat pertanyaan' : 'Balik kartu untuk melihat jawaban');
        find('front').setAttribute('aria-hidden', String(flipped));
        find('back').setAttribute('aria-hidden', String(!flipped));
        find('answer-actions').hidden = !flipped;
    };

    const render = (state, index) => {
        const current = state.cards[index];
        find('question').textContent = current.question;
        find('answer').textContent = current.answer;
        find('position').textContent = `Kartu ${index + 1} dari ${state.stats.total}`;
        find('progress-text').textContent = `${state.stats.reviewed} dari ${state.stats.total} kartu dijawab`;
        find('progress').max = state.stats.total;
        find('progress').value = state.stats.reviewed;
        flip(false);
    };

    const lock = (busy, state, index) => {
        root.querySelectorAll('button').forEach((button) => { button.disabled = busy; });
        if (!busy && state) {
            find('previous').disabled = index === 0;
            find('next').disabled = index === state.cards.length - 1;
            find('shuffle').disabled = state.cards.length < 2;
        }
        root.setAttribute('aria-busy', String(busy));
    };

    return { find, flip, render, lock, isFlipped: () => flipped,
        message: (text) => { find('study-status').textContent = text; } };
}
