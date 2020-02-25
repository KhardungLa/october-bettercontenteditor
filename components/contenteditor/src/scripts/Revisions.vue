<template>
    <div class="revision-selector" v-if="active && hasRevision">
        <ul class="uk-iconnav">
            <li v-if="!editMode"><i uk-icon="icon: history" @click="setEditMode(true)"></i></li>
            <li @click="undo()" v-if="editMode"><i uk-icon="icon: close"></i></li>
            <li @click="setEditMode(false)" v-if="editMode"><i uk-icon="icon: check" ></i></li>
        </ul>
        <transition name="fade">
            <ul class="uk-nav uk-nav-default revision-list" v-if="editMode">
                <li :class="{'uk-active': setVersion === -1}" @click="setRevision(-1)">Akutell</li>
                <li :class="{'uk-active': setVersion === index}" v-for="(revision, index) in revisions" @click="setRevision(index)">
                    {{revision.date}}
                </li>
            </ul>
        </transition>
    </div>
</template>

<script>
    export default {
        props: ['file', 'component'],
        data() {
            return {
                currentRegion: null,
                editMode: false,
                active: false,
                hasRevision: false,
                currentVersion: '',
                setVersion: -1,
                revisions: [],
                initialized: false,
                isFixture: null
            }
        },
        mounted() {
            this.isFixture = document.querySelector(`*[data-file="${this.file}"]`).hasAttribute('data-fixture');
            editor.addEventListener('start', () => {
                this.active = true;
                if (!this.initialized) {
                    this.getRevisions();
                }
            });
            editor.addEventListener('stopped', () => {
                this.active = false;
                this.setVersion = -1;
            });
        },
        methods: {
            setEditMode(state) {
                this.editMode = state;
                if (state && !this.initialized) {
                    this.getRevisions();
                }
            },
            getRevisions() {
                $.request(
                    this.component,
                    {data: {file: this.file}, success: data => {
                        setTimeout(() => {
                            const emptyElements = document.querySelector(`*[data-file="${this.file}"] .ce-element--empty`);
                            if (emptyElements) {
                                emptyElements.remove();
                            }
                            const oldContent = ContentTools.EditorApp.get().regions();
                            if (oldContent[this.file]) {
                                this.currentRegion = oldContent[this.file];
                                this.currentVersion = this.currentRegion.html();
                                if (this.isFixture) {
                                    this.currentVersion = $(this.currentVersion)[0].innerHTML.trim();
                                }
                            } else {
                                this.hasRevision = false;
                            }
                        }, 200);
                        this.revisions = data.filter(revision => revision.old_value !== null);
                        this.hasRevision = this.revisions.length > 0;
                    }}
                );
            },
            setRevision(index) {
                const newContent = index === -1 ? this.currentVersion : this.revisions[index].old_value;
                if (this.isFixture) {
                    document.querySelector(`*[data-file="${this.file}"]`).innerHTML = newContent;
                } else {
                    this.currentRegion.setContent(newContent);
                }
                setTimeout(() => {
                    const emptyElements = document.querySelector(`*[data-file="${this.file}"] .ce-element--empty`);
                    if (emptyElements) {
                        emptyElements.remove();
                    }
                    setTimeout(() => {
                        const emptyElements = document.querySelector(`*[data-file="${this.file}"] .ce-element--empty`);
                        if (emptyElements) {
                            emptyElements.remove();
                        }
                    }, 1000);
                });
                this.setVersion = index;
            },
            undo() {
                this.setRevision(-1);
                this.setEditMode(false);
            }
        }
    }
</script>
<style lang="scss">
    .revision-selector {
        position: absolute;
        margin-top: -30px;
        z-index: 100;
        .revision-list {
            background: rgba(#fff, .9);
            border: solid 1px #999;
            padding: 10px;
            li {
                color: #222 !important;
                text-align: left;
            }
        }
        li {
            &.uk-active {
                font-weight: bold;
            }
        }
    }
</style>
