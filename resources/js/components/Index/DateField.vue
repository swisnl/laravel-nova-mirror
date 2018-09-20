<template>
    <span class="whitespace-no-wrap">{{ formattedDate }}</span>
</template>

<script>
export default {
    props: ['resourceName', 'field'],

    computed: {
        formattedDate() {
          if (this.field.format) {
              return moment(this.field.value).format(this.field.format)
          }

          return this.field.value
        }
    }
}
</script>
