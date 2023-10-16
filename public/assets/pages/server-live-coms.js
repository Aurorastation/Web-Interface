/**
 * Copyright (c) 2016 "Werner Maisl"
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
        faxstatus: "Loading Faxmachines",
        faxtitle: "",
        faxbody: "",
        faxstamptext: "by the Central Command Quantum Relay",
        faxannounce: true,
        faxtargets: "",
        faxmachines: ["Loading Faxmachines..."],
        faxresponse: null,
        faxccia: false,
        faxcciareceivername: "",
        faxcciareceiverrank: "",
        faxcciareceiverstation: "SCCV Horizon",
        faxcciasendername: "",
        faxcciasenderrank: "",
        faxcciasenderstation: "NTCC Odin",
        faxcciasignature: "",
        faxcciasignaturei: false,
        faxcciasignatureb: false,
        faxcciasignatureu: false,
        faxcciaroundtime: "",

        report: "Ready",
        reporttitle: "",
        reportbody: "",
        reporttype: "ccia",
        reportsender: "",
        reportannounce: true

    },

    computed: {
        faxcciasender: function(){
            return this.faxcciasendername + ", " + this.faxcciasenderrank + ", " + this.faxcciasenderstation;
        },

        faxcciareceiver: function(){
            return this.faxcciareceivername + ", " + this.faxcciareceiverrank + ", " + this.faxcciareceiverstation;
        },

        faxdtgstring: function(){
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear()+442;

            return dd + "-" + this.faxcciaroundtime + "-TAU CETI STANDARD-" + mm + "-" + yyyy
        },

        cciafaxready: function(){
            return  this.faxtitle !== "" &&
                this.faxbody !== "" &&
                this.faxstamptext !== "" &&
                this.faxtargets !== "" &&
                this.faxcciareceivername !== "" &&
                this.faxcciareceiverrank !== "" &&
                this.faxcciareceiverstation !== "" &&
                this.faxcciasendername !== "" &&
                this.faxcciasenderrank !== "" &&
                this.faxcciasenderstation !== "" &&
                this.faxcciasignature !== "" &&
                this.faxcciaroundtime !== ""
        },

        faxcciabody: function(){
            return  'TO: <b>'+this.faxcciareceiver +'</b>\r\n' +
                    'FROM: <b>'+this.faxcciasender +'</b>\r\n' +
                    'SUBJECT: <b>'+this.faxtitle+'</b>\r\n' +
                    '<hr />' +
                    'BODY: \r\n' +
                    this.faxbody +
                    '<hr />' +
                    'DTG: <b>'+this.faxdtgstring+'</b>\r\n' +
                    'SIGN: <i>'+this.faxcciasignature+'</i>'
        }
    },

    ready: function(){
        //Get the available faxmachines
        this.getfaxmachines();
    },

    methods: {
        getfaxmachines: function()
        {
            this.$http.get('/server/live/faxmachines').then(
            function(response){
                this.$set('faxmachines',response.json());
                this.$set('faxstatus','Waiting for User Input')
            },
            function(response)
            {
                console.log("Failed to get the fax machines");
                this.$set('faxstatus',"Error getting faxmachines")
            }
        );
        },
        sendfax: function()
        {
            this.$set('faxstatus','Sending the fax ...');
            if(this.faxccia)
            {
                this.$http.post('/server/live/sendfax', {
                    'faxtitle': this.faxtitle,
                    'faxbody': this.faxcciabody,
                    'faxtargets': this.faxtargets,
                    'faxannounce': this.faxannounce,
                    'faxstamptext': this.faxstamptext
                }).then(
                    function(response){
                        this.$set('faxstatus','Fax has been sent');
                        this.$set('faxresponse',response.json())
                        console.log("Fax Sent");
                        console.log(response.json());
                    },
                    function(response){
                        this.$set('faxstatus','Failed to send the fax');
                        console.log("Failed to send the fax");
                    });
            }
            else
            {
                this.$http.post('/server/live/sendfax', {
                    'faxtitle':this.faxtitle,
                    'faxbody':this.faxbody,
                    'faxtargets': this.faxtargets,
                    'faxannounce': this.faxannounce,
                    'faxstamptext': this.faxstamptext
                }).then(
                    function(response){
                        this.$set('faxstatus','Fax has been sent');
                        this.$set('faxresponse',response.json())
                        console.log("Fax Sent");
                        console.log(response.json());
                    },
                    function(response){
                        this.$set('faxstatus','Failed to send the fax');
                        console.log("Failed to send the fax");
                    });
            }
        },
        sendreport: function()
        {
            this.$set('faxstatus','Sending the fax ...');
            this.$http.post('/server/live/sendreport', {
                'reporttitle':this.reporttitle,
                'reportbody':this.reportbody,
                'reporttype': this.reporttype,
                'reportsender': this.reportsender,
                'reportannounce': this.reportannounce
            }).then(
                function(response){
                    //this.$set('faxstatus','Fax has been sent');
                    //this.$set('faxresponse',response.json())
                    console.log("Report Sent");
                    console.log(response.json());
                },
                function(response){
                    //this.$set('faxstatus','Failed to send the command Report');
                    console.log("Failed to send the Report");
                });
        }
    }
});