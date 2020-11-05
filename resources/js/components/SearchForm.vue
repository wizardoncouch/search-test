<template>
    <div class="container">
        <div class="row justify-content-center margin-200">
            <div class="col-md-8">
                <div class="card border-0 bg-transparent">
                    <div class="card-header border-0 bg-transparent"><strong>Wizard Search</strong></div>

                    <div class="card-body search-form border-0 bg-transparent">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" ref="input" aria-label="Search" aria-describedby="inputGroup-sizing-lg" 
                            @focus="showBox" v-model="keyword" />
                            <div class="input-group-append">
                                <span class="input-group-text" id="inputGroup-sizing-lg">Search</span>
                            </div>
                        </div>
                        <div class="relative" v-show="showPopup" v-closable="{ exclude: ['input'], handler: 'onClose' }">
                            <div v-show="fetched && results.length">
                                <a class="d-block" v-for="(item, index) in results" :key="index" @click="selectItem(item.id)" v-html="item.name">
                                </a>
                            </div>

                            <transition name="fade">
                                <div>
                                    <div class="loading p-3" v-show="loading">
                                        Loading...
                                    </div>
        <!--                             <div class="text-center p-3" v-show="!loading && !procedures.length">
                                        No results found
                                    </div>
         -->                        </div>
                            </transition>
                        </div>
                    </div>
                    <div class="card-footer border-0 bg-transparent text-center">
                        @copy 2020
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    let handleOutsideClick
    Vue.directive('closable', {
        bind (el, binding, vnode) {
            handleOutsideClick = (e) => {
                e.stopPropagation();
                const { handler, exclude } = binding.value;
                let clickedOnExcludedEl = false;
                exclude.forEach(refName => {
                    if (!clickedOnExcludedEl) {
                        const excludedEl = vnode.context.$refs[refName];
                        if(excludedEl){
                            clickedOnExcludedEl = excludedEl.contains(e.target);
                        }
                    }
                })
                if (!el.contains(e.target) && !clickedOnExcludedEl) {
                    vnode.context[handler]();
                }
            }
            document.addEventListener('click', handleOutsideClick)
            document.addEventListener('touchstart', handleOutsideClick)
        },
        unbind () {
            document.removeEventListener('click', handleOutsideClick)
            document.removeEventListener('touchstart', handleOutsideClick)
        }
    });
    export default {
        name: 'SearchForm',
        data: () => ({
            loading: false,
            fetched:true,
            showPopup: true, //false
            timeout: null,
            keyword: '',
            results: [{'name':'the wizard'}, {'name':'is sleeping'}],
        }),
        methods: {
            highlight: function(words){
                if(this.fetched){
                    let iQuery = new RegExp(this.keyword, "ig");
                    return words.toString().replace(iQuery, function(matchedTxt,a,b){
                        return ('<span class=\'highlight\'>' + matchedTxt + '</span>');
                    });
                }else{
                    return words;
                }
            },
            onClose() {
                this.showPopup = false
            },
            showBox() {
                if (!this.showPopup && this.keyword.length > 1) {
                    this.showPopup = true;
                    this.results = [];
                    this.search(this.keyword);
                }
            },

            search(keyword){
                axios.get('/api/item/search', {
                    params: {
                        'keyword': keyword
                    }
                })
                    .then(({data}) => {
                        this.results = data;
                        this.loading = false;
                        this.fetched = true;
                    })
                    .catch((error) => {
                        this.loading = false;
                        console.log(error);
                    })
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        watch:{
            keyword: function(n, o){
                console.log(n);
                if (_.trim(n) !== _.trim(o)) {
                    clearTimeout(this.timeout);
                    if (n.length > 1) {
                        let self = this;
                        this.timeout = setTimeout(async function () {
                            self.showPopup = true;
                            self.search(n);
                            clearTimeout(self.timeout);
                        }, 1000);
                    } else {
                        this.onClose();
                    }
                }
            }
        }
    }
</script>
<style lang="scss">
    .search-form {
        .relative {
            position: relative;
            /*margin-left: 64px;*/
        }

        .absolute {
            position: absolute;
            top: 0px;
            left: 0px;
            z-index: 9999;
            background: #ffffff;
            width: 100%;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 3px;
            max-height: 250px;
            overflow-y: auto;
        }

        .loading {
            text-align: center;
            // color: #fff;
            z-index: 9;
            color: #d1d3e2;
        }

        .fade-enter-active, .fade-leave-active {
            transition: opacity .5s
        }

        .fade-enter, .fade-leave-to {
            opacity: 0
        }

        a:hover {
            background-color: #f8f9fc;
        }
        .highlight {
            font-weight: bold;
        }
    }
</style>
