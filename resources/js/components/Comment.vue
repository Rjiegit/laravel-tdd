<template>
    <li class=" media" :name="'comment' + id" :id="'comment' + id">
        <div class="media-left">
            <!-- user avatar-->
        </div>

        <div class="media-body">
            <div class="media-heading mt-0 mb-1 text-secondary">
                <a :title="this.attributes.owner.name" v-text="this.attributes.owner.name">
                </a>
                <span class="text-secondary"> • </span>

                <a class="float-right" >
                    <span class="meta text-secondary" :title="ago" v-text="ago"></span>
                </a>

            </div>
            <div class="comment-content text-secondary" v-html="attributes.content">
            </div>

            <div v-if="signedIn">
                <small class="media-body meta text-secondary">
                    <comment-affect :comment="attributes"></comment-affect>
                </small>
            </div>
        </div>
    </li>
</template>

<script>
import CommentAffect from './CommentAffect';
import moment from 'moment';

export default {
    props: ['attributes'],

    components: { CommentAffect },

    data() {
        return {
            id: this.attributes.id,
            content: this.attributes.content,
        };
    },

    computed: {
        signedIn() {
            return window.App.signedIn;
        },
        ago() {
            return moment(this.attributes.creaated_at).fromNow();
        }
    },
}
</script>
