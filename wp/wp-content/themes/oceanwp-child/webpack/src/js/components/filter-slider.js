const onInput = (parent, slides, inputsNumber, e) => {
    const min = parseFloat(slides[0].min);
    const max = parseFloat(slides[0].max);

    let slide1 = parseFloat(slides[0].value);
    let slide2 = parseFloat(slides[1].value);

    const percentageMin = ((slide1 - min) / (max - min)) * 100;
    const percentageMax = ((slide2 - min) / (max - min)) * 100;

    parent.style.setProperty('--range-slider-value-low', percentageMin);
    parent.style.setProperty('--range-slider-value-high', percentageMax);

    if (slide1 > slide2) {
        const tmp = slide2;
        slide2 = slide1;
        slide1 = tmp;

        if (e.currentTarget === slides[0]) {
            slides[0].insertAdjacentElement('beforebegin', slides[1]);
        } else {
            slides[1].insertAdjacentElement('afterend', slides[0]);
        }
    }
    if(e){
        inputsNumber[0].value = slide1
        inputsNumber[1].value = slide2
    }
    parent.querySelector('.range-slider__display').setAttribute('data-low', slide1);
    parent.querySelector('.range-slider__display').setAttribute('data-high', slide2);
}
function onInputNumber(range, inputsNumber, slides){
    const minValue = Math.min(inputsNumber[0].value, inputsNumber[1].value);
    const maxValue = Math.max(inputsNumber[0].value, inputsNumber[1].value);
    slides[0].value = minValue
    slides[1].value = maxValue
    onInput(range, slides, inputsNumber);
}
addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('.filter-slider')
        .forEach(range => {
            const slides = range.querySelectorAll('.range-slider input');
            const inputsNumber = range.querySelectorAll('.input-number__wrapper input');
            let timeoutID;
            slides.forEach((input) => {
                    input.addEventListener( 'input', e => onInput(range,slides, inputsNumber, e));
                    onInput(range,  slides, inputsNumber);
            })
            inputsNumber.forEach((input) => {
                input.addEventListener( 'input', e => {
                    clearTimeout(timeoutID)
                    timeoutID = setTimeout(()=> onInputNumber(range, inputsNumber, slides), 1000)
                });
            })
        })
});