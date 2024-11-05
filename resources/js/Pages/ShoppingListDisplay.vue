<template>
    <div>
        <h3>Liste de courses :</h3>
        <ul>
            <li v-for="(item, index) in shoppingList" :key="index">{{ item }}</li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        shoppingList: {
            type: Array,
            required: true,
        },
    },
};
</script>

<style scoped>
ul {
    list-style-type: none;
    padding: 0;
}
</style>
