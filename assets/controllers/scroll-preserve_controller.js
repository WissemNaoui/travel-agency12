import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('click', this.preserveScroll.bind(this));
    }

    preserveScroll(event) {
        event.preventDefault();
        const scrollPosition = window.scrollY;
        const href = this.element.getAttribute('href');
        
        sessionStorage.setItem('scrollPosition', scrollPosition);
        window.location.href = href;
    }

    disconnect() {
        const scrollPosition = sessionStorage.getItem('scrollPosition');
        if (scrollPosition) {
            window.scrollTo(0, parseInt(scrollPosition));
            sessionStorage.removeItem('scrollPosition');
        }
    }
}
