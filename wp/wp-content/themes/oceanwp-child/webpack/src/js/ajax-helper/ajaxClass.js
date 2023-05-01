import 'whatwg-fetch'; // for IE support fetch
import 'promise-polyfill'; // for IE support promise

export default class ajaxClass {
    submit() {
        if (!this.isSubmit) {
            this.isSubmit = true;
            this.toggleDisableElem()
            this.toggleLoading()
            let getQueryString = obj => this.getQueryArray(obj).join('&');

            window.fetch('/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: getQueryString(this.data),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cache-Control': 'no-cache',
                },
            }).then(response => response.json())
                .then(data => {
                    this.isSubmit = false;
                    this.toggleLoading()
                    this.toggleDisableElem()
                    this.ajaxSuccessResponse(data)
                })
                .catch(error => {
                    this.isSubmit = false;
                    this.toggleLoading()
                    this.toggleDisableElem(data)
                    this.ajaxErrorResponse(error)
                })


        }
    }

    ajaxSuccessResponse(data) {
        console.log(data);
    }

    ajaxErrorResponse(error, data) {
        console.log(error);
    }

    toggleDisableElem() {
        if (this.hasOwnProperty('disableElems')) {
            if (this.isSubmit) {
                this.disableElems.forEach(elem => {
                    elem.style.opacity = '.5'
                    elem.style.pointerEvents = 'none'
                });
            } else {
                this.disableElems.forEach(elem => {
                    elem.style.opacity = '1'
                    elem.style.pointerEvents = 'auto'
                });
            }
        }

    }

    toggleLoading() {
        if (this.hasOwnProperty('animateLoading')) {
            if (this.isSubmit) {
                if (this.statusParent) this.statusParent.style.display = 'block'
                if (this.animateLoading) this.animateLoading.style.display = 'block'
            } else {
                if (this.statusParent) this.statusParent.style.display = 'none'
                if (this.animateLoading) this.animateLoading.style.display = 'none'
            }
        }
    }

    getQueryArray(obj, path = [], result = []) {
        return Object.entries(obj).reduce((acc, [k, v]) => {
            path.push(k);

            if (v instanceof Object) {
                this.getQueryArray(v, path, acc);
            } else {
                acc.push(`${path.map((n, i) => i ? `[${n}]` : n).join('')}=${v}`);
            }

            path.pop();

            return acc;
        }, result);
    }
}