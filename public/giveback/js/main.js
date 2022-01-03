$(function() {
	"use strict";

	var $svenContainer = $(".sven-container");
	var freezeProp = $svenContainer.attr("data-freeze-prop") ? parseBool($svenContainer.attr("data-freeze-prop")) : true;

	/********************************************************
	Get name parameter from url(Christmas Greeting Demo)
	*********************************************************/
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	};

	var userMessage = getUrlParameter('name');
	var videoID = getUrlParameter('videoid');
	if(userMessage) {
		$(".name-holder").html(userMessage);
	}
	if(videoID) {
		$svenContainer.attr("data-static-video", videoID);
	}

	/********************************************************
	Initiate Countdown here
	*********************************************************/
	$("#sven-countdown").countdown("2017/02/06", function(event) {
		// Add countdown to the last scene
		$(".days-left, .big-days-left").text(
			event.strftime('%D days left')
		);

		$(".big-days").text(
			event.strftime('%D days')
		);

		$(".sven-timer").text(
			event.strftime('%H:%M:%S')
		);
	});

	/********************************************************
	Set autostart to false on mobiles
	*********************************************************/
	var mobileDetected = isMobile.phone; // You can use "isMobile.any" to target both phones and tablets
	var autoStartProp = true; // By default, autoStart is set to "true"
	if(mobileDetected) {
		autoStartProp = false;
	}

	/********************************************************
	Initiate Sven Teaser Plugin
	*********************************************************/
	$svenContainer.svenPlugin({

		// General Options
		autoStart: autoStartProp,
		fullDuration: "default",

		// preload options
		preloadMethod: "tag",
		preloadFiles: [],
		fileTimeout: 8000,
		audioTimeout: 8000,

		// other options
		colors: ["#E7464F", "#CDAA20", "#80993B", "#07BABA", "#9B2C9D"],
		showAnimationSummary: false,
		freezeOnBlur: freezeProp,
		videoPlaybackChange: false,

		// callback functions
		// 1- onTeaserReady, 2- onTeaserStart, 3- onTeaserEnd
		// 4- onBeforeScene, 5- onBeforeIn, 6 - onBeforeFreeze
		// 7- onBeforeOut, 8- onAfterScene
		onTeaserReady: function () {
			// Hide youtube video
			$(".mbYTP_wrapper").css("visibility", "hidden");
			// Hide preloader here
			$(".loader-container").hide();

			// Show Splash Page content
			if(!autoStartProp) {
				$(".splash-page").show();
				$(".sven-footer").show();
			}
			$("#particles-js").appendTo('.sven-wrapper').hide();
		},
		onTeaserStart: function() {
			$(".sven-container").focus();

			// show controls here
			$(".controls-nav").css({
				visibility: "visible"
			});

			// Hide Splash Page Here
			$('.splash-page').hide();

			// Hide Footer Here
			$(".sven-footer").hide();
		},
		onBeforeScene: function(scenePos, $currentScene, durationInfo, masterTS) {
			// Show particles animation only on the last scene
			if($currentScene.data().showParticles) {
				$("#particles-js").show();
			} else {
				$("#particles-js").hide();
			}
		},
		onBeforeIn: function(scenePos, $currentScene, durationInfo, masterTS) {
			if($currentScene.data().showFooter) {
				$(".sven-footer").show();
			} else {
				$(".sven-footer").hide();
			}

			// Show other elements in class ".content-container"
			var $otherElements = $currentScene.find(".content-container").children().not(".content-wrapper");
			if($otherElements.length > 0) {
				TweenMax.staggerFromTo($otherElements, 0.5, {autoAlpha: 0}, {delay: 2, autoAlpha: 1}, 0.08);
			}
		},
		onBeforeOut: function(scenePos, $currentScene, durationInfo, masterTS) {

			// Hide other elements in in class ".content-container"
			var $otherElements = $currentScene.find(".content-container").children().not(".content-wrapper");
			if($otherElements.length > 0) {
				TweenMax.staggerTo($otherElements, 0.25, {autoAlpha: 0}, 0.08);
			}
		}
	});

	/********************************************************
	Teaser Controls and it's actions
	*********************************************************/
	/* 1. Playback Speed */
	var $speedLabel = $('.speed-label span');
	var speedArray = [1, 0.5, 0.25, 1, 1.5, 2];
	var speedIndex = 1;
	var speedLength = speedArray.length;

	$('.speed-label').on('click', function(ev) {
		var curSpeed = speedArray[speedIndex];
		var dispSpeed = isFloat(curSpeed) ? curSpeed : curSpeed.toFixed(1);
		$speedLabel.html(dispSpeed + "x");
		if (speedIndex < speedLength - 1) {
			speedIndex = speedIndex + 1;
		} else {
			speedIndex = 0;
		}
		$svenContainer.svenPlugin.changeSpeed(curSpeed);
	});

	/* 2. Skip To Last Scene */
	$('.skip-button').on('click' , function(ev) {
		$svenContainer.svenPlugin.skipToLast();
	})

	/* 3. Play / Pause / Restart Teaser */
	$('.movie-button, .play-button').on('click' , function(ev) {
		$svenContainer.svenPlugin.togglePlay();
	});

	// Play / Pause teaser by pressing "SPACEBAR" on keyboard.
	$(document).on('keydown', function(e) {
		if (!$('#subscribe-page, #personalize-page').is(':visible') && e.keyCode === 32) {
			$svenContainer.svenPlugin.togglePlay();
		}
	});

	/* 4. Mute / UnMute Teaser Sound */
	$('.sound_button').on('click' , function(ev) {
		$svenContainer.svenPlugin.toggleSound();
	});

	// The teaser triggers a specific event for each state. We change icons of controls here
	var $playIcon = $(".movie-button i");
	var $volumeIcon = $(".sound_button i");

	$svenContainer.on("STPlay", function () {
		$playIcon.removeClass("fa-play").removeClass("fa-repeat").addClass("fa-pause");
	});

	$svenContainer.on("STPause", function () {
		$playIcon.removeClass("fa-pause").removeClass("fa-repeat").addClass("fa-play");
	});

	$svenContainer.on("STEnd", function () {
		$playIcon.removeClass("fa-pause").removeClass("fa-play").addClass("fa-repeat");
	});

	$svenContainer.on("STMuted", function () {
		$volumeIcon.removeClass("fa-volume-up").addClass("fa-volume-off");
	});

	$svenContainer.on("STUnMuted", function () {
		$volumeIcon.removeClass("fa-volume-off").addClass("fa-volume-up");
	});

	/********************************************************
	Personalized URL generation (Christmas Greeting Card)
	*********************************************************/
	$('#personalize input').keyup(function() {
        var empty = false;
        $('#personalize input').each(function() {
            if ($(this).val() == '') {
                empty = true;
            }
        });

        if (empty) {
            $('#submit-form').attr('disabled', 'disabled');
        } else {
            $('#submit-form').removeAttr('disabled');
        }
    });
	$("#personalize [type='submit']").on('click submit', function(event) {
		var message = $("#userMessage").val();
		var videoID = YouTubeGetID($("#videoId").val());
		var url = [location.protocol, '//', location.host, location.pathname].join('');
		var clipboard = new Clipboard('.btn', {
    		text: function(trigger) {
        		return url + "?name=" + message + "&videoid=" + videoID;
    		}
		});
		clipboard.on('success', function(e) {
			$('.personalize-label').html("URL Copied");
			TweenMax.fromTo($('.personalize-label'), 0.25, {opacity: 0, x: "100%"}, {opacity: 1, x: "0%", ease: Linear.easeNone});
		});
		event.preventDefault();
	});

	function YouTubeGetID(url){
		var ID = '';
		url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
		if(url[2] !== undefined) {
			ID = url[2].split(/[^0-9a-z_\-]/i);
			ID = ID[0];
		}
		else {
			ID = url;
		}
		return ID;
	}

	/********************************************************
	email subscription mailChimp(PHP + AJAX)
	*********************************************************/
	$("#subscription [type='submit']").on('click submit', function(event) {
		var formData = $("#subscription").serialize();
		var $inputBoxes = $('input, [type=\'submit\']', "#subscription");
		$inputBoxes.prop('disabled', true);
		$('.subscribe-label').css("visibility", "hidden");
		$('.subscribe-label').css("visibility", "visible").html('<i class="fa fa-hourglass-start"></i>adding your email...');
		var url = "php/subscribe.php";
		$.ajax({
			type: "POST",
			url: url,
			data: formData, // serializes the form's elements.
			dataType: 'json',
			success: function(data) {
				if (data.error) {
					$('.subscribe-label').css("visibility", "hidden");
					$('.subscribe-label').removeClass("error success").addClass("error").css("visibility", "visible").html('<i class="fa fa-times"></i>' + data.message);
					$inputBoxes.prop('disabled', false);
				} else {
					$('.subscribe-label').css("visibility", "hidden");
					$('.subscribe-label').removeClass("error success").addClass("success").css("visibility", "visible").html('<i class="fa fa-check"></i>' + data.message);
				}
			},
			error: function() {
				$('.subscribe-label').css("visibility", "hidden");
				$('.subscribe-label').removeClass("error success").addClass("error").css("visibility", "visible").html('<i class="fa fa-times"></i>Problem connecting to server. Please try again');
				$inputBoxes.prop('disabled', false);
			}
		});
		event.preventDefault();
	});

	/********************************************************
	Preloader Fallback for ie9 (Temporary Solution)
	*********************************************************/
	if (navigator.userAgent.indexOf('MSIE') != -1)
	    var detectIEregexp = /MSIE (\d+\.\d+);/ //test for MSIE x.x
	else // if no "MSIE" string in userAgent
	    var detectIEregexp = /Trident.*rv[ :]*(\d+\.\d+)/ //test for rv:x.x or rv x.x where Trident string exists

	if (detectIEregexp.test(navigator.userAgent)) { //if some form of IE
	    var ieversion=new Number(RegExp.$1) // capture x.x portion and store as a number
	    if (ieversion<=9) {
	        $('body').addClass("loader-ie9");
	    }
	}

	/********************************************************
	Particles animation configuration
	Please visit http://vincentgarreau.com/particles.js/
	*********************************************************/
	if($("#particles-js").length) {
		particlesJS("particles-js", {
			"particles": {
				"number": {
					"value": 50,
					"density": {
						"enable": true,
						"value_area": 800
					}
				},
				"color": {
					"value": "#ffffff"
				},
				"shape": {
					"type": "circle",
					"stroke": {
						"width": 0,
						"color": "#000000"
					},
					"polygon": {
						"nb_sides": 5
					},
					"image": {
						"src": "img/github.svg",
						"width": 100,
						"height": 100
					}
				},
				"opacity": {
					"value": 0.2,
					"random": false,
					"anim": {
						"enable": false,
						"speed": 1,
						"opacity_min": 0.1,
						"sync": false
					}
				},
				"size": {
					"value": 3,
					"random": true,
					"anim": {
						"enable": false,
						"speed": 40,
						"size_min": 0.1,
						"sync": false
					}
				},
				"line_linked": {
					"enable": true,
					"distance": 150,
					"color": "#ffffff",
					"opacity": 0.4,
					"width": 1
				},
				"move": {
					"enable": true,
					"speed": 6,
					"direction": "none",
					"random": false,
					"straight": false,
					"out_mode": "out",
					"bounce": false,
					"attract": {
						"enable": false,
						"rotateX": 600,
						"rotateY": 1200
					}
				}
			},
			"interactivity": {
				"detect_on": "canvas",
				"events": {
					"onhover": {
						"enable": true,
						"mode": "grab"
					},
					"onclick": {
						"enable": true,
						"mode": "push"
					},
					"resize": true
				},
				"modes": {
					"grab": {
						"distance": 140,
						"line_linked": {
							"opacity": 1
						}
					},
					"bubble": {
						"distance": 400,
						"size": 40,
						"duration": 2,
						"opacity": 8,
						"speed": 3
					},
					"repulse": {
						"distance": 200,
						"duration": 0.4
					},
					"push": {
						"particles_nb": 4
					},
					"remove": {
						"particles_nb": 2
					}
				}
			},
			"retina_detect": true
		});
	}
});
