/**
 * Copyright (c) 2016 "Arrow768"
 *
 * This file is part of Aurorastation-Wi
 * Aurorastation-Wi is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */


new Vue({
    el: '#app',
    data: {
        ghosts: {},
        ghostselected: null,
        ghoststatus: "Ready to load Ghost List",
        test: false,
    },

    ready: function(){

    },

    methods: {
        getghosts: function() {
            this.$http.get('/server/live/ghostlist').then(
                function (response) {
                    this.$set('ghosts', response.json());
                    this.$set('ghoststatus', 'Ghosts loaded')
                },
                function (response) {
                    console.log("Failed to get the ghosts");
                    this.$set('ghoststatus', "Error getting ghosts")
                })
        },
        grantrespawn: function() {
            this.$http.post('/server/live/grantrespawn', {
                'target':this.ghostselected
            }).then(
                function (response) {
                    console.log(response.json());
                    this.$set('ghoststatus', 'Respawn Granted')
                },
                function (response) {
                    console.log("Failed to get the ghosts");
                    this.$set('ghoststatus', "Error granting respawn")
                })
        }
    }
});
Vue.config.devtools = true;