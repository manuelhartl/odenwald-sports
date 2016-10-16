﻿	const version = "V10.05";
	const root = "/alpha";
	
	// view_use_strict 'use strict';
	'use strict';

	
		// 10.05	- Data:	log für alle REST Funktionen eingebaut
		//					
		// 10.04	- Data:	Tourfunktion einbauen
		//					leave, join tour
		//
		// 10.02/03	- Data:	Umstellung auf alpha
		//
		// 10.01	- Data:	Umstellung auf alpha
		//


	////////////////////////////// helper ///////////////////////////
	Array.prototype.contains = function (obj) {
		var i = this.length;
		while (i--) {
			if (this[i] === obj) {
				return true;
			}
		}
		return false;
	}
	
	// format:
	//	1: dd.mm.yyyy (default)
	//	2: d.m.yyyy
	//	3: yyyy
	//	4: d.m.yyyy h:MM
	
	function makeDateString(theDate, what) {
		if (theDate != null && theDate.getFullYear() > 0) {
			switch (what) {
			case 2:
				return (theDate.getDate() + "." + (theDate.getMonth() + 1) + "." + theDate.getFullYear());
				break;
			case 3:
				return (theDate.getFullYear());
				break;
			case 4:
				return (theDate.getDate() + "." + (theDate.getMonth() + 1) + "." + theDate.getFullYear() + " " + theDate.getHours() + ":" + pad(theDate.getMinutes()));
				break;
			case 1:
			default:
				return (pad(theDate.getDate()) + "." + pad(theDate.getMonth() + 1) + "." + theDate.getFullYear());
				break;
			}
		}
		return ("");
	}
	
	// format: Meter oder Kilometer mit kurzer oder langer Bezeichnung
	function makeDistanceString(distance, shortInfo) {
		if (!isInteger(distance)) {
			distance = getInteger(distance);
		}
		if (distance != 0 && distance != Number.MAX_SAFE_INTEGER) {
			var val;
			var unit;
			if (distance < 10000) {
				val = distance;
				unit = shortInfo ? "m" : "Meter";
			} else {
				val = round(distance / 1000, (shortInfo ? 0 : 3));
				unit = shortInfo ? "km" : "Kilometer";
			}
			return (val + " " + unit);
		}
		return (shortInfo ? "N/A" : "nicht angegeben");
	}
	
	// format: Höhe mit kurzer oder langer Bezeichnung
	function makeAltitudeString(altitude, shortInfo) {
		if (!isInteger(altitude)) {
			altitude = getInteger(altitude);
		}
		if (altitude != 0 && altitude != Number.MAX_SAFE_INTEGER) {
			var val;
			var unit;
			if (altitude < 10000) {
				val = altitude;
				unit = shortInfo ? "hm" : "H\u00f6henmeter";
			} else {
				val = round(altitude / 1000, 3);
				unit = shortInfo ? "hkm" : "H\u00f6henkilometer";
			}
			return (val + " " + unit);
		}
		return (shortInfo ? "N/A" : "nicht angegeben");
	}
	
	// format: Zeit mit kurzer odr langer Bezeichnung
	function makeDurationString(duration, shortInfo) {
		if (isDateObj(duration)) {
			duration = round(duration.getTime() / 1000 / 60, 0);
		}
		if (!isInteger(duration)) {
			duration = getInteger(duration);
		}
		if (duration != 0 && duration != Number.MAX_SAFE_INTEGER) {
			var t = new Date(duration * 60 * 1000);
			var d = Math.floor(duration / 60 / 24);
			var h = t.getHours() - 1;
			var m = t.getMinutes();
			if (shortInfo) {
				h = d * 24 + h;
				return (h + ":" + pad(m) + " h");
			} else {
				return ((d > 0 ? (d + " Tage ") : "") + (h > 0 ? (h + " Std. ") : "") + (m > 0 ? (m + "Min.") : ""));
			}
		}
		return (shortInfo ? "N/A" : "nicht angegeben");
	}
	//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	//:::                                                                         :::
	//:::  This routine calculates the distance between two points (given the     :::
	//:::  latitude/longitude of those points). It is being used to calculate     :::
	//:::  the distance between two locations using GeoDataSource (TM) prodducts  :::
	//:::                                                                         :::
	//:::  Definitions:                                                           :::
	//:::    South latitudes are negative, east longitudes are positive           :::
	//:::                                                                         :::
	//:::  Passed to function:                                                    :::
	//:::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :::
	//:::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :::
	//:::    unit = the unit you desire for results                               :::
	//:::           where: 'M' is statute miles (default)                         :::
	//:::                  'K' is kilometers                                      :::
	//:::                  'm' is meters                                          :::
	//:::                  'N' is nautical miles                                  :::
	//:::                                                                         :::
	//:::  Worldwide cities and other features databases with latitude longitude  :::
	//:::  are available at http://www.geodatasource.com                          :::
	//:::                                                                         :::
	//:::  For enquiries, please contact sales@geodatasource.com                  :::
	//:::                                                                         :::
	//:::  Official Web site: http://www.geodatasource.com                        :::
	//:::                                                                         :::
	//:::               GeoDataSource.com (C) All Rights Reserved 2015            :::
	//:::                                                                         :::
	//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

	function distance(lat1, lon1, lat2, lon2, unit) {
		var radlat1 = Math.PI * lat1 / 180;
		var radlat2 = Math.PI * lat2 / 180;
		var theta = lon1 - lon2;
		var radtheta = Math.PI * theta / 180;
		var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
		dist = Math.acos(dist);
		dist = dist * 180 / Math.PI;
		dist = dist * 60 * 1.1515;
		if (unit == "K") {
			dist = dist * 1.609344;
		} else if (unit == "m") {
			dist = dist * 1609.344;
		} else if (unit == "N") {
			dist = dist * 0.8684;
		}
		return (dist);
	}
	
	function getDistance(lat1, lon1, lat2, lon2){
		var dist;
		if(lat1!=0&&lon1!=0&&lat2!=0&&lon2!=0){
			dist = distance(lat1, lon1, lat2, lon2, "m");
		}else{
			dist=0;
		}
		
		return( makeDistanceString(dist, true) );
	}
	
	function isDate(txtDate) {
		var currVal = txtDate;
		if (currVal == '') {
			return (false);
		}
		var rxDatePattern = /^(\d{1,2})(\/|.)(\d{1,2})(\/|.)(\d{4})$/; //Declare Regex
		var dtArray = currVal.match(rxDatePattern); // is format OK?

		if (dtArray == null) {
			return (false);
		}
		// Checks for dd/mm/yyyy format.
		dtMonth = dtArray[3];
		dtDay = dtArray[1];
		dtYear = dtArray[5];

		if (dtMonth < 1 || dtMonth > 12)
			return (false);
		else if (dtDay < 1 || dtDay > 31)
			return (false);
		else if ((dtMonth == 4 || dtMonth == 6 || dtMonth == 9 || dtMonth == 11) && dtDay == 31)
			return (false);
		else if (dtMonth == 2) {
			var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
			if (dtDay > 29 || (dtDay == 29 && !isleap))
				return (false);
		}
		return (true);
	}
	
	function isDateObj(obj) {
		/// <summary>
		/// Determines if the passed object is an instance of Date.
		/// </summary>
		/// <param name="obj">The object to test.</param>
		return Object.prototype.toString.call(obj) === '[object Date]';
	}
	function isValidDate(obj) {
		/// <summary>
		/// Determines if the passed object is a Date object, containing an actual date.
		/// </summary>
		/// <param name="obj">The object to test.</param>
		return !isEmpty(obj) && isDateObj(obj) && obj.toString()!='Invalid Date';
	}

	function isEmpty(obj){
		return (jQuery.isEmptyObject(obj));
	}
	
	function sh(value){
		return((value!=null&&value.length!=0)?value:".");
	}
	
	function pad(n){ return n<10 ? '0'+n : n; };
	
	function toggle(id){
		var e = document.getElementById(id);
		
		if(e !=null ){
			e.style.display = (e.style.display == "none")?"":"none";
		}
 	}
	
	function isInteger(x) {
        return (typeof x === 'number') && (x % 1 === 0);
    }
	
	function isNumber(x) {
        return (typeof x === 'number');
    }
	
	function getInteger(value){
		var val = Number.MAX_SAFE_INTEGER;
		if(isInteger(value)){
			val = value;
		}else if(isNumber(value)){
			val = Math.floor(value);
		}else if(isString(value)){
			var v = value.replace(/[^0-9 ]/g, '');
			val = parseInt( v );
		}
		return(val);
	}
	
	function isString(x) {
		return !isEmpty(x) && x !== undefined && x.constructor === String
	}
	
	function round(wert, dez) {
        wert = parseFloat(wert);
        if (!wert) return 0;

        dez = parseInt(dez);
        if (!dez) dez=0;

        var umrechnungsfaktor = Math.pow(10,dez);

        return ((Math.round(wert * umrechnungsfaktor) / umrechnungsfaktor).toFixed(dez).replace('.', ','));
	} 
	
	function date2JSON(d){
		var	tz = d.getTimezoneOffset(), // mins
			tzs = (tz>0?"-":"+") + pad(parseInt(Math.abs(tz/60)));

		if (tz%60 != 0){
			tzs += pad(Math.abs(tz%60));
		}
		if (tz === 0){ // Zulu time == UTC
			tzs = 'Z';
		}
		
		return (d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds()) + tzs);
	}
	
	function dateFromServer(dateJSON){
		var a = dateJSON.split(/[^0-9]/);
		return( new Date (a[0], a[1]-1, a[2]|| 1, a[3]|| 0, a[4]|| 0, a[5]|| 0 ));
	}
	
	function dateFromLokal(dateJSON){
		var a = dateJSON.split(/[^0-9]/);
		return( new Date (Date.UTC(a[0], a[1]-1, a[2]|| 1, a[3]|| 0, a[4]|| 0, a[5]|| 0 )));
	}
	
	function testDate(){
		var date = new Date(2015,6,11,16,00,00);
		var dateJSON = date2JSON(date);
		var serverJSON = "2015-07-11T16:00:00+0200";
		var serverDate = dateFromServer(serverJSON);
		var localDateJSON = date2JSON(serverDate);
		var localDate = dateFromLokal(localDateJSON);
		
		console.log("Original date      : " + date);
		console.log("Original date(JSON): " + dateJSON);
		console.log("Server date (JSON) : " + serverJSON);
		console.log("Server date        : " + serverDate);
		console.log("Local date (JSON)  : " + localDateJSON);
		console.log("Local date         : " + localDate);
	}			
	
	function getDateFromString4Safari(dateString){
		if(dateString==null) {return(null);}
		var a = dateString.split(/[^0-9]/);
		var date;
		if (dateString.substring( dateString.length - "Z".length, dateString.length ) === "Z") {
			date = new Date (a[0], a[1]-1, a[2]|| 1, a[3]|| 0, a[4]|| 0, a[5]|| 0 );
		}else{
			date = new Date (Date.UTC(a[0], a[1]-1, a[2]|| 1, a[3]|| 0, a[4]|| 0, a[5]|| 0 ));
		}
		return(date);
	}

	function dump(text, obj){
		var str = JSON.stringify(obj, null, 4); // (Optional) beautiful indented output.
		console.log(text);
		console.log(str=="undefined"?obj:str);
	}
	////////////////////////////// end-helper ///////////////////////////


	// relocation service : goto functions
	function go2LoginPage(){ saveData1("lo"); window.location.href='login.html'; };
	function go2MailPage(mailtype, tour, user, linkOK, linkCancel){
		if(mailtype == "2tourall"){
			setTempStorage("mail", "type", mailtype);			// Mail an Alle
			setTempStorage("mail", "source", tour);		 
			setTempStorage("mail", "subsource", "");
			setTempStorage("mail", "destination_success", linkOK);
			setTempStorage("mail", "destination_cancel", linkCancel);
		}else if(mailtype == "2tourguide"){
			setTempStorage("mail", "type", mailtype);			// Mail an Tour Guide
			setTempStorage("mail", "source", tour);		 
			setTempStorage("mail", "subsource", "");
			setTempStorage("mail", "destination_success", linkOK);
			setTempStorage("mail", "destination_cancel", linkCancel);
		}else if(mailtype == "2tourmember"){
			setTempStorage("mail", "type", mailtype);			// Mail an Tour Member
			setTempStorage("mail", "source", tour);		 
			setTempStorage("mail", "subsource", user);
			setTempStorage("mail", "destination_success", linkOK);
			setTempStorage("mail", "destination_cancel", linkCancel);
		}else if(mailtype == "2user"){
			setTempStorage("mail", "type", mailtype);			// Mail an user
			setTempStorage("mail", "source", "");		 
			setTempStorage("mail", "subsource", user);
			setTempStorage("mail", "destination_success", linkOK);
			setTempStorage("mail", "destination_cancel", linkCancel);
		}
		saveData1("ma");
		window.location.href='mail.html';
	}
	function go2RegisterPage(){ saveData1("re"); window.location.href='register.html'; };
	function go2ProfilPage(profil_nickname){ setTempStorage("profil", "profil_nickname", profil_nickname); saveData1("pr"); window.location.href='profil.html'; };
	function go2StatisticPage(){ saveData1("st"); window.location.href='statistic.html'; };
	function go2TourPage(tourID, modifiable, okDestination, cancelDestination){ 
		setTempStorage("tour", "tourid", tourID);
		setTempStorage("tour", "modifiable", modifiable);
		setTempStorage("tour", "okDestination", okDestination);
		setTempStorage("tour", "cancelDestination", cancelDestination);
		saveData1("to");
		window.location.href='tour.html'; 
	};
	function go2TourlistPage(){ saveData1("tl"); window.location.href='tour-list.html'; };
	function go2UserlistPage(){ saveData1("ul"); window.location.href='user-list.html'; };
	function go2Destination(destination){
		if(destination == "ul"){
			go2UserlistPage();
		}else if(destination == "tl"){
			go2TourlistPage();
		}else if(destination == "pr"){
			go2ProfilPage();
		}else if(destination == "st"){
			go2StatisticPage();
		}else if(destination == "lo"){
			go2LoginPage();
		}else if(destination == "re"){
			go2RegisterPage();
		}
	}
	////////////////////////////// END: goto functions ///////////////////////////

	function showAJAXError(what, xhr, ajaxOptions, thrownError){
		// showAJAXError(this.url, xhr, ajaxOptions, thrownError);
		console.log("Fehler bei   : " + what);
		console.log(" ajaxOptions : " + ajaxOptions);
		console.log(" thrownError : " + thrownError);
		
	}
	function showAJAXSuccess(what, parameter, textstatus, xhr){
		// 	showAJAXSuccess(this.url, parameter);
		console.log("OK bei      : " + what + " textstatus : " + textstatus);
		console.log(" parameter  : " + JSON.stringify(parameter));		console.log();
	}
	
	function Login(un, pw, okDestination, errorDestination){
		if(un!=null&&pw!=null){
			$.ajax({
				async: false,
				method: "POST",
				url: root+"/rest/auth.php",
				data: {
					username: un,
					password: pw
				},
				dataType: "text",
				error_message: "Fehler " + un + " ist kein gültiger Account",
				error_destination: errorDestination,
				error: function( xhr, ajaxOptions, thrownError ) {
					showAJAXError(this.url, xhr, ajaxOptions, thrownError)
					setMe(null);
					setMessage(this.error_message);
					go2Destination(this.error_destination);
				},
				success_nickname: un,
				success_message: un + " eingeloggt",
				succes_destination: okDestination,
				success: function( parameter, textstatus, xhr ) {
					showAJAXSuccess(this.url, parameter, textstatus, xhr);
					setMe(this.success_nickname);
					setMessage(this.success_message);
					data=null;
					go2Destination(this.succes_destination);	
				}
			});
		}else{
			setMessage(un + " ist kein gültiger Account");
			go2Destination(errorDestination);
		}
	}
	
	function WhoAmI(){
		var nickname = "";

		$.ajax({
			async: false,
			url: root+"/rest/whoami.php",
			dataType : "json",
			error: function( xhr, ajaxOptions, thrownError ) {
				showAJAXError(this.url, xhr, ajaxOptions, thrownError)
			},
			success: function( parameter, textstatus, xhr ) {
				showAJAXSuccess(this.url, parameter, textstatus, xhr);
				if(typeof(parameter.authenticated) != undefined){
					if(parameter.authenticated){
						if(parameter.user){
							nickname = parameter.user.username;
						}
					}
				}
			},
		});
		return( nickname );	
	}
	
	function getRSSLink(){
		return("http:////www.sport2gether.de/"+root+"//rss//")
	}
	
	function Logout(){
		var user = self.getMe();
		var nickname = user.nickname;
		
		$.ajax({
			async: false,
			method: "POST",
			url: root+"/rest/logout.php",
			dataType: "text",
			error_message: nickname + ' konnte nicht abgemeldet werden',
			error: function( xhr, ajaxOptions, thrownError ) {
				showAJAXError(this.url, xhr, ajaxOptions, thrownError);
				setMessage(this.error_message);
				go2TourlistPage();
			},
			success_message: nickname + ' wurde abgemeldet',
			success: function( parameter, textstatus, xhr ) {
				showAJAXSuccess(this.url, parameter, textstatus, xhr);
				setMe(null);
				setMessage(this.success_message);
				go2TourlistPage();
			}
		});
	}
	
	function ResetPassword(username, okDestination, errorDestination){
		if(username!=null&&data.getUser(username)!=null){
			setMessage("Neue Mail für " + username + " erzeugt");
			go2Destination(okDestination);	
		}else{
			setMessage(username + " ist kein gültiger Account");
			go2Destination(errorDestination);
		}
	}

	function Register(username, passwort, email, acceptRules, okDestination, errorDestination){ 
		if(username!=null&&data.getUser(username)==null){
			setMessage(username + " registriert, bitte Mail bestätigen [" + username + ', ' + passwort +', ' + email + ', ' + acceptRules + ']');
			go2Destination(okDestination);	
		}else{
			setMessage(username + " ist kein gültiger Account");
			go2Destination(errorDestination);
		}
	}
	
	function SetProfile(username, name, phone, birthday, adress, tourletter, okDestination, errorDestination){		
		if(true){
			var me = data.getUser(username);
			// set profile was successfully
			me.name = name;
			me.phone = phone;
			me.birthday = birthday;
			me.adress = adress;
			me.tourletter = tourletter;
			setMessage("Profil erfolgreich geändert [" + name + " - " + phone + " - " + birthday + " - " + adress + " - " + tourletter + "]");
			go2Destination(okDestination);	 
		}else{
			setMessage("konnte Profile nicht ändern");
			go2Destination(errorDestination);
		}
	}

	function showMessage(info, document, id, duration){
		document.getElementById(id).innerHTML = info;
		setTimeout(function(){ document.getElementById(id).innerHTML = ""; }, duration*1000);
	}		
	
	function toggleActiveTour(tourID){
		var tour = data.getTour(tourID);
		if(updateTour(tour, tour.active?"cancel":"activate", null)){
			setMessage( "Tour " + tourID + " wurde " + (tour.active?"abgesagt":"reaktiviert"));
			tour.active = !tour.active;
		}else{
			setMessage( "Tour " + tourID + " konnte nicht " + (tour.active?"abgesagt":"reaktiviert") + " werden");
		}
		go2TourlistPage();		
	}
	
	function add2Tour(tourID, member){
		var tour = data.getTour(tourID);		
		if(updateTour(tour, "add", member)){		
			tour.add2Tour(member);
			setMessage(member + " wurde erfolgreich an der Tour " + tourID + " angemeldet");	
		}else{
			setMessage(member + " konnte nicht an die Tour " + tourID + " angemeldet werden");	
		}
		go2TourlistPage();		
	}
	
	function removeFromTour(tourID, member){
		var tour = data.getTour(tourID);
		if(updateTour(tour, "remove", member)){		
			tour.removeFromTour(member);
			setMessage(member + " wurde erfolgreich von der Tour " + tourID + " abgemeldet");	
		}else{
			setMessage(member + " konnte nicht von der Tour " + tourID + " abgemeldet werden");	
		}
		go2TourlistPage();
	}
	
	function delegateTour(tour, newGuide){
		var guide = data.getUser(newGuide);
		
		$.ajax({
			method: "POST",
			url: root+"/rest/tour-delegate.php",
			data: {
				id: tour.id,
				guideid: guide.id
			},
			dataType: "json",
			error: function( xhr, ajaxOptions, thrownError ) {
				showAJAXError(this.url, xhr, ajaxOptions, thrownError)
			},
			success: function( parameter, textstatus, xhr ) {
				showAJAXSuccess(this.url, parameter, textstatus, xhr);
				console.log("Versuche Guide " + tour.guide + " zu " + guide.nickname + " ändern");
			}
		});
	}
		
	function updateTour(tour, what, nickname){
		var retValue = false;
		var dest= "";
	
		switch(what){
			case "activate":	dest = "tour-activate.php";
								break;
			case "cancel":		dest = "tour-cancel.php";
								break;
			case "add":			dest = "tour-join.php";
								break;
			case "remove":		dest = "tour-leave.php";
								break;
		}
	
		// set data
		var parameter =	{ "id": tour.id	};
		
		if(nickname!=null){
			parameter["member"] = data.getUser(nickname).id;
		}
		$.ajax({
			async: false,
			method: "POST",
			url: root+"/rest/"+dest,
			data: JSON.stringify(parameter),
			dataType: "json",
			error: function( xhr, ajaxOptions, thrownError ) {
				showAJAXError(this.url, xhr, ajaxOptions, thrownError)
			},
			success: function( parameter, textstatus, xhr ) {
				showAJAXSuccess(this.url, parameter, textstatus, xhr);

				// TODO: GW set date from return values
				data.lastUpdate = getDate();

				retValue = true;
			}
		});
		
		return( retValue );
	}
	
	function saveTourObject(tour){
		var retValue = false;
		data.lastUpdate = getDate();
		var tourtypeid = data.getTourTypebyType(tour.tourtype).tourtypeid;
		var guide = data.getUser(tour.guide);
		
		var t =	{
					"tours": [{
								"id":		tour.id,			
								"desc":		tour.description,
								"datetime":	date2JSON(tour.datetime),
								"distance":	tour.distance,
								"speed":		tour.pace,
								"duration":	tour.duration,
								"elevation":	tour.up,
								"skill":		tour.technic,
								"sport": {
									"id":		tourtypeid
								},
								"meetingpoint": {
									"name":		tour.meetingpoint,
									"desc":		tour.adresse,
									"lat":		tour.latitude,
									"long":		tour.longitude
								},
								"guide": {
									"id":		guide.id,
									"name":		guide.nickname
								},
								"attendees": []
							}]
			};
		
		//
		if(tour.id<0){
			delete t.tours[0].id;
		}
				
		$.ajax({
			async: false,
			method: "POST",
			url: root+"/rest/tour-save.php",
			data: JSON.stringify(t),
			dataType: "json",
			error: function( xhr, ajaxOptions, thrownError ) {
				showAJAXError(this.url, xhr, ajaxOptions, thrownError)
			},
			success: function( parameter, textstatus, xhr ) {
				showAJAXSuccess(this.url, parameter, textstatus, xhr);
				// übernehme aus response die neue tour ID
				if(tour.id<0){
					tour.id = parameter.id;
				}
				// TODO: GW set date from return values
				data.lastUpdate = getDate();
				retValue = true;
			}
		});
		
		return( retValue );		
	}
	
	function saveTour(id, guide, members, datetime, duration, distance, up, pace, technic, meetingpoint, adresse, lat, lon, tourtype, description, night, active, destination){
		var tour = null;
		if(id <= 0){
			tour = new Tour(-1, guide, members, datetime, duration, distance, up, pace, technic, meetingpoint, adresse, lat, lon, tourtype, description, night, active);
		}else{
			tour = data.getTour(id);
			tour.datetime = datetime;
			tour.duration = duration;
			tour.distance = distance;
			tour.up = up;
			tour.pace = pace;
			tour.technic = technic;
			tour.meetingpoint = meetingpoint;
			tour.adresse = adresse;
			tour.tourtype = tourtype;
			tour.description = description;
			tour.active = active;
		}
		if(saveTourObject(tour)){
			if(id <= 0){
				data.tours().push(tour);
			}else{
				// guide changed?
				if(tour.guide != guide){
					delegateTour(tour, guide);
				}
				// members changed?
				if(tour.members.length==members.length && tour.members.every(function(v,i) { return v === members[i]})){
					var diff_lefttour = $(tour.members).not(members).get();
					var diff_addtour = $(members).not(tour.members).get();
					
					console.log("Remove from to " + JSON.stringify(diff_lefttour));
					console.log("Add to " + JSON.stringify(diff_addtour));
					
					// TODO: GW set date from return values
					data.lastUpdate = getDate();
				}
			}
			setMessage((id <= 0?"Neue Tour wurde eingegeben":"Tour wurde geändert"));
		}else{
			setMessage("ein Fehler ist beim speichern der Tour aufgetreten");
		}		
		go2Destination(destination);
	}
	
	// get date from the server
	function getDate(){
		var theDate = new Date();
		$.ajax({
			async: false,
			url: root+"/rest/servertime.php",
			error: function( xhr, ajaxOptions, thrownError ) {
				showAJAXError(this.url, xhr, ajaxOptions, thrownError)
			},
			success: function( parameter, textstatus, xhr ) {
				showAJAXSuccess(this.url, parameter, textstatus, xhr);
				if(typeof(parameter.servertime) != undefined){
					theDate = dateFromServer(parameter.servertime);
				}
			}
		});	
		return( theDate );
	}
	
	function getUserFromServer( userJson ){
		var latitude = 0, longitude = 0;
		if(userJson.address_gps){
			latitude = userJson.address_gps.lat;
			longitude = userJson.address_gps.long;
		}
		return(new User(userJson.username, userJson.id, userJson.realname, userJson.address, latitude, longitude, userJson.birthdate!=null?dateFromServer(userJson.birthdate):null, userJson.phone, true, dateFromServer(userJson.registerdate)));
		
	}

	function getUsers() {
		var theUsers = [];
		$.ajax({
			async: false,
			url: root+"/rest/users.php",
			error: function( xhr, ajaxOptions, thrownError ) {
				showAJAXError(this.url, xhr, ajaxOptions, thrownError)
			},
			success: function( parameter, textstatus, xhr ) {
				showAJAXSuccess(this.url, parameter, textstatus, xhr);
				if(parameter!="not logged in"){
					if((typeof(parameter.users) != undefined)&&(parameter.users.length>0)){
						parameter.users.forEach(function(userJson) {
							theUsers.push(getUserFromServer(userJson));
						});
					}
				}
			}
		});
		return(theUsers);
	}

	function getTours() {
		var theTours = [];
		$.ajax({
			async: false,
			url: root+"/rest/tours.php",
			error: function( xhr, ajaxOptions, thrownError ) {
				showAJAXError(this.url, xhr, ajaxOptions, thrownError)
			},
			success: function( parameter, textstatus, xhr ) {
				showAJAXSuccess(this.url, parameter, textstatus, xhr);
				if(parameter!="not logged in"){
					if((typeof(parameter.tours) != undefined)&&(parameter.tours.length>0)){
						var auth = typeof(parameter.tours[0].attendees)!= undefined&&parameter.tours[0].attendees;
						parameter.tours.forEach(function(t){
							var tour;
							var tt = t.sport.subname.toLowerCase();
							var members= [];
							if(auth){
								// handle members
								t.attendees.forEach( function(a) { members.push(a.name); });
								tour = new Tour(t.id, t.guide.name, members, dateFromServer(t.datetime), t.duration, t.distance, t.elevation, t.speed, t.skill, t.meetingpoint.desc, t.meetingpoint.name, t.meetingpoint.lat, t.meetingpoint.long, tt, t.desc, false, !t.canceled);
							}else{
								tour = new Tour(   0,           "", members, dateFromServer(t.datetime), t.duration, t.distance, t.elevation, t.speed, t.skill,                  "",                  "",                  0,                   0, tt, t.desc, false, true);
								if("attendees_count" in t){
									for(i = 0; i<t.attendees_count; i++){
										members.push(".");
									}
								}
							}

							theTours.push(tour);
						});
					}
				}
			}
		});
		return(theTours);
	}

	// class to represent a user
	function User(nickname, id, name, adress, latitude, longitude, birthday, phone, tourletter, registration) {
		var self = this;
		
		self.nickname = nickname;
		self.id = id;
		self.name = name!=null?name:"";
		self.adress = adress!=null?adress:"";
		self.latitude = latitude;
		self.longitude = longitude;
		self.distance = function(latitude, longitude) { return (getDistance(latitude, longitude, self.latitude, self.longitude)); };
		self.birthday = birthday;
		self.getBirthday = function() { return( makeDateString(self.birthday,3));}
		self.phone = phone!=null?phone:"";
		self.tourletter = tourletter;
		self.registration = registration;
		self.getRegistration = function() { return( makeDateString(self.registration,1)); }
		
		self.userPhoneInfo = function() { return ("Für " + self.nickname + (phone?" Telefon: " + phone : " liegen keine Kontaktdaten vor")); }; 
		self.userMailTitle = function() { return ("Mail an " + self.nickname); };
		self.userMail = function(sender){ return ("Mail an " + self.nickname + " von " + sender); };
		
		self.guidedTours = ko.observableArray([]).extend({ rateLimit: 50 });
		self.guidedToursNo = ko.pureComputed( function() { return(self.guidedTours().length); } );

		self.toursGuided = ko.observableArray([]).extend({ rateLimit: 50 });
		self.toursGuidedNo = ko.pureComputed( function() { return(self.toursGuided().length); } );
	}
	
	// class to represent a tour
	//             1    2      3         4        5          6       7     8      9		   10           11        12       13          14         15       16      17 
	function Tour(id, guide, members, datetime, duration, distance, up, pace, technic, meetingpoint, adresse, latitude, longitude, tourtype, description, night, active) {
		var self = this;
		
		self.id = id;
		self.guide = guide;
		self.correctable = function(nickname){ return( self.guide==nickname && self.id>0 && self.oldTour() );}; // TODO: GW auf x Stunden der Vergangenheit beschränken
		self.members = members;
		self.datetime = datetime;
		// todo GW: implement function
		self.getDate = function() { return("01.01.2016") };
		self.getTime = function() { return("10:00") };
		self.duration = duration;
		self.durationText = function() { return(makeDurationString( self.duration, true)); };    						
		self.distance = distance;
		self.distanceText = function() { return(makeDistanceString( self.distance, true)); };  		
		self.up = up;
		self.upText = function() { return(makeAltitudeString( self.up, true)); };    			

		self.pace = pace;
		self.getPaceCSS = function() { return('tl-icon-rate-' + pace); };
		self.technic = technic;
		self.getTechnicCSS = function() { return('tl-icon-rate-' + technic); };
	
		self.meetingpoint = meetingpoint;
		self.meetingpointInfo = function(latitude, longitude) { var a = self.adresse; var d = getDistance(latitude, longitude, self.latitude, self.longitude); return(a + " [" + d + "]");};
		self.adresse = adresse;
		self.latitude = latitude;
		self.longitude = longitude;
		self.night = function() { return(datetime.getHours()>=0&&datetime.getHours()<=5||datetime.getHours()>=20&&datetime.getHours()<=24); }; // night;
		self.active = active;
		self.tourtype = tourtype;
		self.getTourtypeCSS = function() { return(data.getTourTypebyType(self.tourtype).icon + (self.night()?" tl-nightaction ":"") + (self.active?"":" img-b"));};
		self.getTourtypeCanceledCSS = function() { return( "tl-icon-sport-canceled img-a");};
		self.getTourtypeOldCSS = function() { return( "tl-icon-sport-history disable img-c");};
		
		self.description = description;
		self.shorttourday = function() { return( self.datetime.getDate()  + "." + (self.datetime.getMonth()+1) + "." + self.datetime.getFullYear() ); };
		self.tourday = function() {	var days = ['So','Mo','Di','Mi','Do','Fr','Sa']; return( days[self.datetime.getDay()] + ", " + self.shorttourday() ); };
		
		self.tourtime = function() { return(pad(self.datetime.getHours()) + ":" + pad(datetime.getMinutes())); };    						
		self.isEven = function() { return(((self.datetime.getTime()/(1000*60*24)))%2==0); }; 
		
		self.tourEditTitle = function() { return ("Die Tour am " + self.tourday() + " um " + self.tourtime() + " bearbeiten"); };
		self.tourCopyTitle = function() { return ("Die Tour am " + self.tourday() + " um " + self.tourtime() + " kopieren"); };
		self.tourActivateTitle = function() { return ("Die Tour am " + self.tourday() + " um " + self.tourtime() + " wieder aktivieren"); };
		self.tourDeactivateTitle = function() { return ("Die Tour am " + self.tourday() + " um " + self.tourtime() + " absagen"); };
		
		self.tourRegisterTitle = function() { return ("Bei der Tour am " + self.tourday() + " um " + self.tourtime() + " anmelden"); };
		self.tourUnRegisterTitle = function() { return ("Bei der Tour am " + self.tourday() + " um " + self.tourtime() + " abmelden"); };
		// info for mail
		self.tourMail2Guide = function(tourmember) { return ("Mail von " + tourmember + " zur Tour am " + self.tourday() + " um " + self.tourtime()); };
		self.tourMail2Tour = function(tourmember) { return (self.tourMail2Guide(tourmember+ " an Alle")); };
		self.tourMail2User = function() { return (self.tourMail2Guide(self.guide)); };
		
		
		self.shorttourDescription = function() { return(self.shorttourday() + " mit " + self.guide + " um " + self.tourtime() + " ab Treffpunkt " + self.meetingpoint ); };		
		//self.tourDescription = function() { return(self.tourday() + " um " + self.tourtime() + " ab Treffpunkt " + self.meetingpoint + (self.guide!=null?" mit " + self.guide:"") ); };
		
		self.tourMembers = function() { return(self.members.length); };
		self.oldTour = function(now){ return(now>=self.datetime.getTime()); };
		
		self.isTourMember = function(member) {return(self.members.contains(member));};
		self.isPossibleTourMember = function(searchMember){
			var m = searchMember.toLowerCase();
			for (var i = 0, len = self.members.length; i < len; i++) {
				if(self.members[i].toLowerCase().indexOf(m) != -1){
					return(true);
				}
			}						
			return(false);
		};
		self.getMembersString = function(all){
			var membersstring= all?self.guide + ", ":"";
			for (var i = 0, len = self.members.length; i < len; i++) {
				membersstring += self.members[i] + ", ";
			}
			
			return(membersstring.substring(0, membersstring.length-2));
		}
		self.isPossibleTourGuide = function(searchGuide){ return(self.guide.toLowerCase().indexOf(searchGuide.toLowerCase()) != -1);};
		self.add2Tour = function(member){
			if(!self.isPossibleTourMember(member)){
				self.members.push(member);
			}
		};
		self.removeFromTour = function(member){
			var m=[];
			for (var i = 0, len = self.members.length; i < len; i++) {
				if(self.members[i]!=member){
					m.push(self.members[i]);
				}
			}						
			self.members=m;
		}
	}
	
	function cloneTour(tour){
		var m = [];
		for (var i = 0, len = tour.members.length; i < len; i++) {
			m.push(tour.members[i]);
		}						
		
		var new_tour = new Tour(tour.id, tour.guide, m, tour.datetime, tour.duration, tour.distance, tour.up, tour.pace, tour.technic, tour.meetingpoint, tour.adresse, tour.latitude, tour.longitude, tour.tourtype, tour.description, tour.night, tour.active);

		return(new_tour);
	}
	
	function DataModell() {
		var self = this;

		self.versioninfo = version;
		
		self.lastUpdate = null;
		
		self.users = ko.observableArray().extend({ rateLimit: 50 });		
		self.getUser = function(nickname){ return(ko.utils.arrayFirst(self.users(), function(user) {return (user.nickname == nickname);}));	};
		
		self.tours = ko.observableArray().extend({ rateLimit: 50 });
		self.getTour = function(id){ return(ko.utils.arrayFirst(self.tours(), function(tour) {return (tour.id == id);})); };
		
		var sort_year = 0;
		self.setStatistics = function(year){
			if(self.users().length>0){
				if(sort_year!=year){
					sort_year = year;
					// reset statistics
					self.tours().forEach(function(t,i,ts){ self.getUser(t.guide).guidedTours([]); t.members.forEach(function(m,i,ts){ self.getUser(m).toursGuided([]) }); });
					// set statistic by criteria
					self.tours().forEach(function(t,i,ts){
						if(t.active && (year==-1?true:t.datetime.getFullYear()==year) ){
							var g = self.getUser(t.guide);
							g.guidedTours.push(t.id);
							t.members.forEach(function(m,i,ts){
								var u = self.getUser(m);
								if(u==null){
									dump("Member not found", m);
								}
								u.toursGuided.push(t.id);
							});
						}
					});	
				}
			}				
		}
		
		self.initFromServer = function(lastUpdate, theUsers, theTours){
			self.lastUpdate = lastUpdate;
			self.users(theUsers);
			self.tours(theTours);
			self.setStatistics(-1);
		};
		
		self.toJSON = function(){
			return(ko.toJS(self));
		}
		
		self.initFromJSON = function (dataJSON){
			//var json = JSON.parse(dataJSON);
			var json = dataJSON;
			
			var theUsers = [];
			if((typeof(json.users) != undefined)&&(json.users.length>0)){
				json.users.forEach(function(u) {
					var u1 = new User(u.nickname, u.id, u.name, u.adress, u.latitude, u.longitude, u.birthday!=null?dateFromLokal(u.birthday):null, u.phone, true, dateFromLokal(u.registration));
					theUsers.push(u1);
				});
			}

			var theTours = [];
			if((typeof(json.tours) != undefined)&&(json.tours.length>0)){
				json.tours.forEach(function(t){
					
					var m = [];
					t.members.forEach( function(a) { m.push(a); });		
					var t1 = new Tour(t.id, t.guide, m, dateFromLokal(t.datetime), t.duration, t.distance, t.up, t.pace, t.technic, t.meetingpoint, t.adresse, t.latitude, t.longitude, t.tourtype, t.description, false, t.active);
					theTours.push(t1);
				});
			}
			var lastUpdate = dateFromLokal(json.lastUpdate);
			
			self.lastUpdate = lastUpdate;
			self.users = ko.observableArray(theUsers);
			self.tours = ko.observableArray(theTours);
			self.setStatistics(-1);
			
		}
		// default values for
		// tour-list
		const tl_showMore = false;
		const tl_showFilter = false;
		const tl_showActiveTours = true;
		const tl_showOldTours = false;
		const tl_showNightlyTours = true;
		const tl_showDeactivateTours = false;
		// tour
		const to_showMore = false;
		const to_showMap = false;
		const to_showSlider = true;
		// userlist
		const ul_showMore = false;
		const ul_showFilter = false;


		// tourtype entspricht sportsubname
		self.tourtypes = [
			{ tourtype: "mtb",  tourtypeid: "1", tourart: "MTB", description: "Geländefahren", icon: "tl-icon-sport-mtb" },
			{ tourtype: "rennrad", tourtypeid: "3", tourart: "Rennrad fahren", description: "Asphalthobeln", icon: "tl-icon-sport-rennrad" },
			{ tourtype: "crosser", tourtypeid: "4", tourart: "Crosser", description: "ist das noch Radfahren", icon: "tl-icon-sport-crosser" },
			{ tourtype: "lauf", tourtypeid: "5", tourart: "Laufen", description: "Platte Füsse", icon: "tl-icon-sport-run" },
			{ tourtype: "trail-running", tourtypeid: "6", tourart: "Trailrun", description: "Aufie", icon: "tl-icon-sport-trailrun" },
			{ tourtype: "schwimmen", tourtypeid: "7", tourart: "Schwimmen", description: "Treiben mit dem Wind", icon: "tl-icon-sport-schwimmen" },
			{ tourtype: "triathlon", tourtypeid: "9", tourart: "Triathlon", description: "3 Sachen, drittel gut", icon: "tl-icon-sport-triathlon" },
			{ tourtype: "duathlon", tourtypeid: "8", tourart: "Duathlon", description: "2 Sachen halb gut", icon: "tl-icon-sport-duathlon" },
			{ tourtype: "swim & bike", tourtypeid: "10", tourart: "Schwimmen & Biken", description: "nur Fatbikes schwimmen", icon: "tl-icon-sport-swim-bike" },
			{ tourtype: "langlauf", tourtypeid: "11", tourart: "Ski-Langlauf", description: "immer nur runter", icon: "tl-icon-sport-ski" },
			{ tourtype: "feier", tourtypeid: "12", tourart: "Feiern", description: "Let's dance", icon: "tl-icon-sport-feier" },
			{ tourtype: "downhill", tourtypeid: "13", tourart: "Downhill", description: "immer nur runter", icon: "tl-icon-sport-downhill-bike" },
			{ tourtype: "e-bike", tourtypeid: "14", tourart: "E-Bike", description: "nur soviel Kraft um das höhere Gewicht auszugleichen", icon: "tl-icon-sport-e-bike" }
		]
		self.getTourTypes = function(){
			var result = [];
			
			ko.utils.arrayForEach(this.tourtypes, function(tourtype) { result.push( tourtype.tourart );});
			return(result);
		}
		self.getTourTypebyType = function(tourtype){
			var result = ko.utils.arrayFirst(self.tourtypes, function(item) {return (item.tourtype == tourtype);});

			if(result==null){ 
				console.log("Can't find tourtype by "+tourtype); 
			}
			return(result);
		};
		self.rate = [
			{ ratetype: "1", icon: "tl-icon-rate-1", stk_description: "Forstwege" ,stk_average: "", stk_max: "", speed: "Kinder geeignet" },
			{ ratetype: "2", icon: "tl-icon-rate-2", stk_description: "leichte Trails", stk_average: "S0", stk_max: "S0", speed: "langsam"  },
			{ ratetype: "3", icon: "tl-icon-rate-3", stk_description: "trailige Wege, max S1", stk_average: "S0", stk_max: "S1", speed: "gemütlich" },
			{ ratetype: "4", icon: "tl-icon-rate-4", stk_description: "anspruchsvollere Trails", stk_average: "S1", stk_max: "S2", speed: "normal" },
			{ ratetype: "5", icon: "tl-icon-rate-5", stk_description: "sehr anspruchsvolle Trails",stk_average: "S2", stk_max: "S3", speed: "zügig" },
			{ ratetype: "6", icon: "tl-icon-rate-6", stk_description: "eher unfahrbar, >S2", stk_average: "S3", stk_max: "S4", speed: "Renntempo"  }
		];
				
		self.getSTKDescription = function( rate ){
			var stk_info = "";
			if(rate.stk_average!=""){
				stk_info = " [Ø: " + rate.stk_average;
				if(rate.stk_max!=""){
					stk_info += ", max: " + rate.stk_max;
				}
				stk_info += " ]";
			}
			return(rate.stk_description + stk_info);
		}
		self.getRateFromRatetype = function(ratetype){ return(ko.utils.arrayFirst(self.rate, function(item) {return (item.ratetype == ratetype);})); };

		var checkBooleanPreference = function(value){
			return(typeof value === 'boolean');
		}
		/* --------- Construction --------- */
		// update user preferences
		if(!checkBooleanPreference(getPreference("tl_showMore"))){ setPreference("tl_showMore",tl_showMore);}
		if(!checkBooleanPreference(getPreference("tl_showFilter"))) { setPreference("tl_showFilter",tl_showFilter);}
		if(!checkBooleanPreference(getPreference("tl_showActiveTours"))) { setPreference("tl_showActiveTours",tl_showActiveTours);}
		if(!checkBooleanPreference(getPreference("tl_showOldTours"))) { setPreference("tl_showOldTours",tl_showOldTours);}
		if(!checkBooleanPreference(getPreference("tl_showNightlyTours"))) { setPreference("tl_showNightlyTours",tl_showNightlyTours);}
		if(!checkBooleanPreference(getPreference("tl_showDeactivateTours"))) { setPreference("tl_showDeactivateTours",tl_showDeactivateTours);}	
		if(getPreference("tl_selectedTourtype")=="") { setPreference("tl_selectedTourtype",ko.toJSON(self.getTourTypes())); }	
		if(!checkBooleanPreference(getPreference("to_showMore"))) { setPreference("to_showMore",to_showMore);}
		if(!checkBooleanPreference(getPreference("to_showMap"))) { setPreference("to_showMap",to_showMap);}		
		if(!checkBooleanPreference(getPreference("to_showSlider"))) { setPreference("to_showSlider",to_showSlider);}		
		if(!checkBooleanPreference(getPreference("ul_showMore"))) { setPreference("ul_showMore",ul_showMore);}
		if(!checkBooleanPreference(getPreference("ul_showFilter"))) { setPreference("ul_showFilter",ul_showFilter);}
	}	
	
	var data = null;
	
	function getData(){
		// TODO: GW get from server
		var lastServerUpdate = new Date (2016,07,01);
		var lastLocalUpdate = getSessionStorage("lastUpdate");
		// does local data exist?
		if(lastLocalUpdate == null || lastLocalUpdate == ""){
			// no, make last update old then server date
			lastLocalUpdate = new Date (1000*60);
		}else{
			lastLocalUpdate = dateFromLokal(lastLocalUpdate);
		}
		if(lastServerUpdate > lastLocalUpdate){
			// get data from server
				// get data from server
				var theUsers = getUsers();
				var theTours = getTours();
				data = new DataModell();
				
				data.initFromServer(lastServerUpdate, theUsers, theTours);
				// and save it
				setSessionStorage("data", data.toJSON());	
				setSessionStorage("lastUpdate", date2JSON(data.lastUpdate));	
		}else{
			// local data is up to date		
			if(data==null){
				// get from local storage
				data = new DataModell();
				data.initFromJSON(getSessionStorage("data"));
			}			
		}
		return(data);
	}
	
	function clearData(){
		data = null;
		removeSessionStorage("data");
		removeSessionStorage("lastUpdate");
	}
	
	function saveData(){
		// TODO GW: Save data
		if(data!=null){				
			// save it
			setSessionStorage("data", data.toJSON());
			setSessionStorage("lastUpdate", date2JSON(data.lastUpdate));	
		}else{
			removeSessionStorage("data");
			removeSessionStorage("lastUpdate");
		}
	}
	
	function saveData1(destination){
		// TODO: GW only if destination changes
		// get actual location
		if(getLocation() != destination){
			saveData();
			
			// save new location
			// soll das von der seite gemacht werden?
			setLocation(destination);
		}
	}
	
	function getMe(){
		//var nickname = getTempStorage("global", "nickname");
		var nickname = WhoAmI();
		return(data!=null&&nickname!=""?data.getUser(nickname):null);
	}
	function setMe(nickname){
		setTempStorage("global", "nickname", nickname);
	}
	
	function setMessage(message){
		setTempStorage("global", "message", message);	
	}
	function getMessage(){
		var message =  getTempStorage("global", "message");	
		setMessage(null);
		return(message);
	}
	
	function setLocation(location){
		setTempStorage("global", "location", location);	
	}
	function getLocation(){
		return(setTempStorage("global", "location"));	
	}
	
	// general function for session handling via windows name
	function setTempStorage( session, name, value ){
		setStorageWN(session + "_" + name, value)
	}
	function getTempStorage( session, name ){
		return(getStorageWN(session + "_" + name))
	}
	function clearTempStorage( session ){
		// remove all entries where key starts with session + "_"
		var keys = [];
		var obj = getAllStorageWN();
		for(var key in obj){
			if(key.startsWith(session + "_")){
				keys.push(key);
			}
		}
		for (i=0;i<keys.length;i++){
			removeStorageWN(keys[i]);
		}
	}

	
	// https://wiki.selfhtml.org/wiki/JavaScript/Anwendung_und_Praxis/Wert%C3%BCbergabe_zwischen_verschiedenen_HTML-Dokumenten
	// handling storage via windows.name
	var getStorageWN = function (name){
		var value = storageWN.get(name);
		return((value!=null&&typeof(value) != undefined)?value:"");
	}
	
	var setStorageWN = function (name, value){
		return(storageWN.set(name, value));
	}
	
	var removeStorageWN = function (name){
		return(storageWN.remove(name));
	}
	
	var getAllStorageWN = function(){
		return(storageWN.getAll());
	}
	
	var storageWN = new function () {
		/* --------- Private Properties --------- */
		var dataContainer = {};
		/* --------- Private Methods --------- */		
		function read () {
			if (window.name == '' || window.name.indexOf(":") == -1) { return; }
				
			dataContainer = JSON.parse(window.name);
		}
		function write () { window.name = ko.toJSON(dataContainer); } 
		
		/* --------- Public Methods --------- */
		this.set = function (name, value) { dataContainer[name] = value; write(); };
		this.get = function (name) { var returnValue = dataContainer[name]; return returnValue; };
		this.getAll = function () { return dataContainer; };
		this.remove = function (name) { if (typeof(dataContainer[name]) != undefined) { delete dataContainer[name]; } write(); };
		this.removeAll = function () { dataContainer = {}; write(); }; 
		/* --------- Construction --------- */
		read();
	};
	
	// handling storage via session storage
	function getSessionStorage(name){
		var value = sessionstorage.get(name); 
		return((value!=null&&typeof(value) != undefined)?value:"");
	}
	
	function setSessionStorage(name, value){
		return(sessionstorage.set(name, value));
	}
	
	function removeSessionStorage(name){
		return(sessionstorage.remove(name));
	}
	
	function removeAllSessionStorage(){
		return(sessionstorage.removeAll());
	}
	
	var sessionstorage = new function () {
		/* --------- Private Properties --------- */
		var useSessionStorage = true;
		var dataContainer = {};
		var db;  // our alias for sessionStorage
		/* --------- Private Methods --------- */
		function checkStorage(){
			try {
					var x = 'test-sessionStorage-' + Date.now();
					sessionStorage.setItem(x, x);
					var y = sessionStorage.getItem(x);
					sessionStorage.removeItem(x);
					if (y !== x) {throw new Error();}
					db = sessionStorage; // sessionStorage is fully functional!
			} catch (e) {
				db = new MemoryStorage('GW-sessionStorage'); // fall back to a memory-based implementation
			}
		}

		function read () {
			dataContainer = {};
			if (useSessionStorage) {
				var ds = db.getItem("dataContainer");
				if(ds!=null && typeof(ds) != undefined){
					dataContainer = JSON.parse(ds);
				}
			}
		}
		
		function write () { if(useSessionStorage){ db.setItem("dataContainer", ko.toJSON(dataContainer));} } 
		
		/* --------- Public Methods --------- */
		this.set = function (name, value) { dataContainer[name] = value; write(); };
		this.get = function (name) { var returnValue = dataContainer[name]; return returnValue; };
		this.getAll = function () { return dataContainer; };
		this.remove = function (name) { if (typeof(dataContainer[name]) != undefined) { delete dataContainer[name]; } write(); };
		this.removeAll = function () { dataContainer = {}; write(); }; 
		/* --------- Construction --------- */
		checkStorage();
		read();
	};
	
	// handling storage via local storage
	function setPreference(name, value){
		setLocalStorage("Pref" + "_" + name, value)
	}
	
	function getPreference(name){
		return(getLocalStorage("Pref" + "_" + name));
	}
	
	function clearPreferences(){
		var keys = [];
		var obj = getAllLocalStorage();
		for(var key in obj){
			if(key.startsWith("Pref" + "_")){
				keys.push(key);
			}
		}
		for (i=0;i<keys.length;i++){
			removeLocalStorage(keys[i]);
		}
	}
	
	function getLocalStorage(name){
		var value = localstorage.get(name); 
		return((value!=null&&typeof(value) != undefined)?value:"");
	}
	
	function setLocalStorage(name, value){
		return(localstorage.set(name, value));
	}
	
	function removeLocalStorage(name){
		return(localstorage.remove(name));
	}
	
	function getAllLocalStorage(){
		return(localstorage.getAll());
	}
	
	var localstorage = new function () {
		/* --------- Private Properties --------- */
		var useLocalStorage = true;
		var dataContainer = {};
		var db;  // our alias for localStorage
		/* --------- Private Methods --------- */
		function checkStorage(){
			try {
					var x = 'test-localstorage-' + Date.now();
					localStorage.setItem(x, x);
					var y = localStorage.getItem(x);
					localStorage.removeItem(x);
					if (y !== x) {throw new Error();}
					this.db = localStorage; // localStorage is fully functional!
			} catch (e) {
				this.db = new MemoryStorage('GW-localstorage'); // fall back to a memory-based implementation
			}
		}
		
		function read () {
			if (useLocalStorage) {
				var ds = this.db.getItem("dataContainer");
				if(ds!=null && typeof(ds) != undefined){
					dataContainer = JSON.parse(ds);
				}
			}
		}
		
		function write () { if(useLocalStorage){ this.db.setItem("dataContainer", ko.toJSON(dataContainer));} } 
		
		/* --------- Public Variable--------- */
		this.hasStorage = checkStorage();
		/* --------- Public Methods --------- */
		this.set = function (name, value) { dataContainer[name] = value; write(); };
		this.get = function (name) { var returnValue = dataContainer[name]; return returnValue; };
		this.getAll = function () { return dataContainer; };
		this.remove = function (name) { if (typeof(dataContainer[name]) != undefined) { delete dataContainer[name]; } write(); };
		this.removeAll = function () { dataContainer = {}; write(); }; 
		/* --------- Construction --------- */
		checkStorage();
		read();
	}

	/* --------- Construction --------- */