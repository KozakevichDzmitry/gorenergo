import ajaxClass from "./ajaxClass";

export default class FilterButtonsClass extends ajaxClass {
    constructor(data, submitButton, actionElems) {
        super();
        this.disableElems = actionElems.disableElems
        this.hiddenElems = actionElems.hiddenElems
        this.statusParent = actionElems.hiddenElems.statusParent
        this.animateLoading = actionElems.animateLoading
        this.data = data;
        if (typeof submitButton !== undefined) {
            this.submitButton = submitButton;
        }
        this.submit();

    }

    ajaxSuccessResponse(data) {
        this.toggleHideElems(data.found_posts, data.loaded_posts)
        this.submitButton.dataset.loaded_posts = data.loaded_posts
        const products = document.querySelector('.products')
        products.outerHTML = data.post
    }

    toggleHideElems(all_posts, loaded_posts) {
        if (loaded_posts >= all_posts) {
            if(this.statusParent) this.statusParent.style.display = "block"
            if(this.hiddenElems) {
                this.hiddenElems.btnLoadMore.style.display = "none"
                if(this.hiddenElems.statusMessage) this.hiddenElems.statusMessage.style.display = "block"
            }

        } else {
            if(this.statusParent) this.statusParent.style.display = "none"
            if(this.hiddenElems) {
                this.hiddenElems.btnLoadMore.style.display = "block"
                if(this.hiddenElems.statusMessage) this.hiddenElems.statusMessage.style.display = "none"
            }
        }
    }


}