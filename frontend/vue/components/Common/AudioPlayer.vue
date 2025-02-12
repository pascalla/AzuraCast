<template>
    <div>
        <audio ref="audio" v-if="isPlaying" v-bind:title="title"/>
    </div>
</template>

<script>
import store from 'store';
import getLogarithmicVolume from '~/functions/getLogarithmicVolume.js';
import vueStore from '~/store.js';

export default {
    props: {
        title: String
    },
    data() {
        return {
            'audio': null,
            'volume': 55,
            'duration': 0,
            'currentTime': 0
        };
    },
    computed: {
        isPlaying() {
            return vueStore.state.player.isPlaying;
        },
        current() {
            return vueStore.state.player.current;
        }
    },
    watch: {
        volume(volume) {
            if (this.audio !== null) {
                this.audio.volume = getLogarithmicVolume(volume);
            }

            if (store.enabled) {
                store.set('player_volume', volume);
            }
        },
        current(newCurrent) {
            let url = newCurrent.url;
            if (url === null) {
                this.stop();
            } else {
                this.play();
            }
        },
    },
    mounted() {
        // Allow pausing from the mobile metadata update.
        if ('mediaSession' in navigator) {
            navigator.mediaSession.setActionHandler('pause', () => {
                this.stop();
            });
        }

        // Check webstorage for existing volume preference.
        if (store.enabled && store.get('player_volume') !== undefined) {
            this.volume = store.get('player_volume', this.volume);
        }

        // Check the query string if browser supports easy query string access.
        if (typeof URLSearchParams !== 'undefined') {
            let urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('volume')) {
                this.volume = parseInt(urlParams.get('volume'));
            }
        }
    },
    methods: {
        stop() {
            if (this.audio !== null) {
                this.audio.pause();
                this.audio.src = '';
            }

            this.duration = 0;
            this.currentTime = 0;

            vueStore.commit('player/stopPlaying');
        },
        play() {
            if (this.isPlaying) {
                this.stop();
                this.$nextTick(() => {
                    this.play();
                });
                return;
            }

            vueStore.commit('player/startPlaying');

            this.$nextTick(() => {
                this.audio = this.$refs.audio;

                // Handle audio errors.
                this.audio.onerror = (e) => {
                    if (e.target.error.code === e.target.error.MEDIA_ERR_NETWORK && this.audio.src !== '') {
                        console.log('Network interrupted stream. Automatically reconnecting shortly...');
                        setTimeout(() => {
                            this.play();
                        }, 5000);
                    }
                };

                this.audio.onended = () => {
                    this.stop();
                };

                this.audio.ontimeupdate = () => {
                    this.duration = (this.audio.duration !== Infinity && !isNaN(this.audio.duration)) ? this.audio.duration : 0;
                    this.currentTime = this.audio.currentTime;
                };

                this.audio.volume = getLogarithmicVolume(this.volume);

                this.audio.src = this.current.url;

                this.audio.load();
                this.audio.play();
            });
        },
        toggle(url, isStream) {
            vueStore.commit('player/toggle', {
                url: url,
                isStream: isStream
            });
        },
        getVolume() {
            return this.volume;
        },
        setVolume(vol) {
            this.volume = vol;
        },
        getCurrentTime() {
            return this.currentTime;
        },
        getDuration() {
            return this.duration;
        },
        getProgress(x) {
            return (this.duration !== 0) ? +((this.currentTime / this.duration) * 100).toFixed(2) : 0;
        },
        setProgress(progress) {
            if (this.audio !== null) {
                this.audio.currentTime = (progress / 100) * this.duration;
            }
        },
    }
};
</script>
