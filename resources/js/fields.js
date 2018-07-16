import Vue from 'vue'

Vue.component('default-field', require('./components/Form/DefaultField.vue'))
Vue.component('field-wrapper', require('./components/Form/FieldWrapper.vue'))

// Panels...
Vue.component('panel', require('./components/Detail/Panel.vue'))
Vue.component('relationship-panel', require('./components/Detail/RelationshipPanel.vue'))

// Text Field...
Vue.component('index-text-field', require('./components/Index/TextField.vue'))
Vue.component('detail-text-field', require('./components/Detail/TextField.vue'))
Vue.component('form-text-field', require('./components/Form/TextField.vue'))

// Password Field...
Vue.component('index-password-field', require('./components/Index/PasswordField.vue'))
Vue.component('detail-password-field', require('./components/Detail/PasswordField.vue'))
Vue.component('form-password-field', require('./components/Form/PasswordField.vue'))

// Textarea Field...
Vue.component('index-textarea-field', require('./components/Index/TextField.vue'))
Vue.component('detail-textarea-field', require('./components/Detail/TextareaField.vue'))
Vue.component('form-textarea-field', require('./components/Form/TextareaField.vue'))

// Code Field...
Vue.component('index-code-field', require('./components/Index/TextField.vue'))
Vue.component('detail-code-field', require('./components/Detail/CodeField.vue'))
Vue.component('form-code-field', require('./components/Form/CodeField.vue'))

// Date Field
Vue.component('index-date', require('./components/Index/DateField.vue'))
Vue.component('form-date', require('./components/Form/DateField.vue'))
Vue.component('detail-date', require('./components/Detail/DateField.vue'))

// DateTime Field...
Vue.component('index-date-time', require('./components/Index/DateTimeField.vue'))
Vue.component('form-date-time', require('./components/Form/DateTimeField.vue'))
Vue.component('detail-date-time', require('./components/Detail/DateTimeField.vue'))

// Boolean Field
Vue.component('index-boolean-field', require('./components/Index/BooleanField.vue'))
Vue.component('detail-boolean-field', require('./components/Detail/BooleanField.vue'))
Vue.component('form-boolean-field', require('./components/Form/BooleanField.vue'))

// Select Box Field
Vue.component('form-select-field', require('@/components/Form/SelectField'))
Vue.component('detail-select-field', require('@/components/Detail/TextField'))
Vue.component('index-select-field', require('@/components/Index/TextField'))

// Upload Field
Vue.component('index-file-field', require('./components/Index/FileField.vue'))
Vue.component('detail-file-field', require('./components/Detail/FileField.vue'))
Vue.component('form-file-field', require('./components/Form/FileField.vue'))

// Status Field...
Vue.component('index-status-field', require('./components/Index/StatusField.vue'))
Vue.component('detail-status-field', require('./components/Detail/StatusField.vue'))
Vue.component('form-status-field', require('./components/Form/StatusField.vue'))

// Markdown Field
Vue.component('index-markdown-field', require('./components/Detail/TextField.vue'))
Vue.component('detail-markdown-field', require('./components/Detail/MarkdownField.vue'))
Vue.component('form-markdown-field', require('./components/Form/MarkdownField.vue'))

// Trix Field
Vue.component('detail-trix-field', require('./components/Detail/TrixField.vue'))
Vue.component('form-trix-field', require('./components/Form/TrixField.vue'))

// Algolia Place Field
Vue.component('form-place-field', require('@/components/Form/PlaceField'))
Vue.component('detail-place-field', require('@/components/Detail/TextField'))
Vue.component('index-place-field', require('@/components/Index/TextField'))

// Has One Field...
Vue.component('detail-has-one-field', require('./components/Detail/HasOneField.vue'))

// Has Many Field...
Vue.component('detail-has-many-field', require('./components/Detail/HasManyField.vue'))

// Belongs To Field...
Vue.component('index-belongs-to-field', require('./components/Index/BelongsToField.vue'))
Vue.component('detail-belongs-to-field', require('./components/Detail/BelongsToField.vue'))
Vue.component('form-belongs-to-field', require('./components/Form/BelongsToField.vue'))

// Belongs To Many Field...
Vue.component('detail-belongs-to-many-field', require('./components/Detail/BelongsToManyField.vue'))

// Morph To Many Field...
Vue.component('detail-morph-to-many-field', require('./components/Detail/MorphToManyField.vue'))

// Morph To Field...
Vue.component('index-morph-to-field', require('./components/Index/MorphToField.vue'))

Vue.component(
    'index-morph-to-action-target-field',
    require('./components/Index/MorphToActionTargetField.vue')
)

Vue.component('detail-morph-to-field', require('./components/Detail/MorphToField.vue'))

Vue.component(
    'detail-morph-to-action-target-field',
    require('./components/Detail/MorphToActionTargetField.vue')
)

Vue.component('form-morph-to-field', require('./components/Form/MorphToField.vue'))
