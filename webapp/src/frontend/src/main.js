import { defineAsyncComponent, defineCustomElement } from 'vue';

const components = import.meta.glob('./components/*.vue');
for (const [filename, component] of Object.entries(components)) {
  const name = filename.replace(/^.\/components\//, '').replace(/\.vue$/, '');
  window.customElements.define(
    name,
    defineCustomElement(defineAsyncComponent(component))
  );
}
