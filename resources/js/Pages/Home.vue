<template>
  <div>
    <h1>{{ title }}</h1>
  </div>
</template>

<script>
export default {
  name: 'Home',
  data() {
    return {
      title: 'ceci est le component'
    }
  }
}
</script>

<style scoped>
h1{
color: black;}
</style>
