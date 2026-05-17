import 'trix';
import 'trix/dist/trix.css';

export const initTrix = () => {
    document.addEventListener('trix-file-accept', (event) => {
        event.preventDefault();
    });
};
