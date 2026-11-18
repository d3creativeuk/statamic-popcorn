import PopcornFieldtype from './components/PopcornFieldtype.vue';

Statamic.booting(() => {
    Statamic.$components.register('popcorn-fieldtype', PopcornFieldtype);
});
