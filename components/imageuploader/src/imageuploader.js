import Vue from 'vue$';
import UIkit from 'uikit';
import './imageuploader.scss';

window.UIkit = UIkit;
Vue.config.devtools = false;
Vue.config.productionTip = false;
new Vue({
    delimiters: ['${', '}'],
    data() {return {usesEditor: false, editMode: true}},
    mounted() {
        setTimeout(() => {
            if (window.editor !== undefined) {
                this.usesEditor = true;
                if (editor._ignition.state() === "ready") {
                    document.querySelectorAll('.js-uploader').forEach(el => {
                        el.style.display = 'none';
                    });
                }
                editor.addEventListener('start', () => {
                    document.querySelectorAll('.js-uploader').forEach(el => {
                        el.style.display = 'block';
                    });
                });
                editor.addEventListener('stopped', () => {
                    document.querySelectorAll('.js-uploader').forEach(el => {
                        el.style.display = 'none';
                    });
                });
            }
        }, 500);
        let element = UIkit.upload('.js-upload', {
            url: 'dasrotequadrat/image/upload',
            multiple: false,
            name: 'image[]',
            beforeAll: component => {
                element = component.$el;
                component.params = {item: element.getAttribute('id')};
            },
            completeAll: xhr => {
                if (element.classList.contains('uses-image')) {
                    element.querySelector('img').src = JSON.parse(xhr.response).url;
                } else {
                    element.style.backgroundImage = `url(${JSON.parse(xhr.response).url})`;
                }
                UIkit.notification('Bild wurde gespeichert');
            }
        });

    }
}).$mount('#image-uploader-app');
