<template>
    <div v-if="signedIn" class="reply-box">
        <div class="form-group">
            <textarea class="form-control" rows="3" placeholder="我要評論" name="content" v-model="content"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-sm" @click="addComment"><i class="fa fa-share mr-1"></i> 發布評論</button>
        <hr>
    </div>
</template>

<script>
export default {
    props: ['endpoint'],

    data() {
        return {
            content:'',
        };
    },

    computed: {
        signedIn() {
            return window.App.signedIn;
        },
    },

    methods: {
        addComment() {
            axios.post(this.endpoint, { content:this.content })
                .then(({data}) => {
                    this.content = '';

                    flash('評論成功!');

                    this.$emit('created', data);
                });
        }
    }
}
</script>
