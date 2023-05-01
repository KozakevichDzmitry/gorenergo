import '../../styles/parts/filter.scss'
import '../components/filter-slider'
import FilterButtonsClass from "../ajax-helper/FilterButtonsClass";

const btnFilter = document.querySelector('#btnToggleFilter')
const filtersWrappers = document.querySelectorAll('.filter')
const btnSubmit = document.querySelector('.filter__footer-btn--submit')
const btnReset = document.querySelector('.filter__footer-btn--reset')
const btnLoadMore = document.querySelector('.load-more__btn')
const statusMessage = document.querySelector('.scroller-status__message')
const statusParent = document.querySelector('.scroller-status')
const category_id = btnFilter.dataset.category_id
const disableElems = [btnSubmit, btnReset, btnLoadMore]
const hiddenElems = {btnLoadMore: btnLoadMore, statusMessage:statusMessage, statusParent:statusParent}
const animateLoading =document.querySelector('.infinite-scroll-request')

if (btnFilter)
    btnFilter.addEventListener('click', (e) => e.currentTarget.classList.toggle('active'))

if (btnSubmit) {
    btnSubmit.addEventListener('click', () => onSubmit(filtersWrappers))
}

if (btnReset) {
    btnReset.addEventListener('click', () => onReset(filtersWrappers))
}
if (btnLoadMore) {
    btnLoadMore.addEventListener('click', (e) => onLoadMore(e))
}


function onSubmit(filtersWrappers) {
    let filters = {
        cat_id: category_id,
        loaded_posts: btnSubmit.dataset.loaded_posts || 0,
        'in': {},
        range: {}
    };
    btnLoadMore.dataset.filter = 'true';
    filtersWrappers.forEach(parent => {
        const filterType = parent.dataset.filter_type
        const taxonomyName = parent.dataset.attribute_slug
        if (filterType === 'checkbox-column' || filterType === 'checkbox-row') {
            const inputs = parent.querySelectorAll('.filter__option input[type="checkbox"]:checked')
            const taxValues = [...inputs].map(input => input.dataset.name).join(',')
            if (taxValues) filters['in'][`${taxonomyName}`] = taxValues
        } else if (filterType === 'slider') {
            const inputs = parent.querySelectorAll('.input-number')
            let min = inputs[0].value
            let max = inputs[1].value
            if (min && !max) max = inputs[1].dataset.name
            else if (!min && max) min = inputs[0].dataset.name

            if (min && max) filters.range[`${taxonomyName}`] = [min, max]
        }
    })
    filters.action = filter.action
    window._filters = filters
    new FilterButtonsClass(_filters, btnSubmit, {disableElems:disableElems, hiddenElems:hiddenElems, animateLoading:animateLoading});
}

function onReset(filtersWrappers) {
    let filters = {
        cat_id: category_id,
        loaded_posts: 0,
    };
    btnLoadMore.dataset.filter = 'true';
    filtersWrappers.forEach(parent => {
        const filterType = parent.dataset.filter_type
        if (filterType === 'checkbox-column' || filterType === 'checkbox-row') {
            const inputs = parent.querySelectorAll('.filter__option input[type="checkbox"]:checked')
            inputs.forEach(input => input.checked = false)
        } else if (filterType === 'slider') {
            const inputs = parent.querySelectorAll('.input-number')
            const inputsRange = parent.querySelectorAll('[type="range"]')
            inputsRange[0].value = inputsRange[0].min
            inputsRange[1].value = inputsRange[1].max
            inputsRange.forEach(input => input.dispatchEvent(new Event('input', {bubbles: true})))
            inputs.forEach(input => input.value = '')
        }
    })
    filters.action = filter.action
    window._filters = filters
    new FilterButtonsClass(_filters, btnSubmit, {disableElems:disableElems, hiddenElems:hiddenElems, animateLoading:animateLoading});
}

function onLoadMore(e) {
    if( e.currentTarget.dataset.filter === 'true'){
        window._filters.loaded_posts = btnSubmit.dataset.loaded_posts
        new FilterButtonsClass(window._filters, btnSubmit, {disableElems:disableElems, hiddenElems:hiddenElems, animateLoading:animateLoading});
    }

}