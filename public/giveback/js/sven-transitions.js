var svenTransitions = {

    /* Vegas Transitions adopted from https://github.com/jaysalvat/vegas*/
    vegas: [

        // kenburns basic
        {
            name : "kenburns",
            in : [{
                from : {
                    scale: 1.5
                },
                to : {
                    scale: 1
                }
            }]
        },

        // kenburns-up
        {
            name : "kenburns-up",
            in : [{
                from : {
                    scale: 1.5,
                    y: "10%"
                },
                to : {
                    scale: 1,
                    y: "0%"
                }
            }]
        },

        // kenburns-down
        {
            name : "kenburns-down",
            in : [{
                from : {
                    scale: 1.5,
                    y: "-10%"
                },
                to : {
                    scale: 1,
                    y: "0%"
                }
            }]
        },

        // kenburns-left
        {
            name : "kenburns-left",
            in : [{
                from : {
                    scale: 1.5,
                    x: "10%"
                },
                to : {
                    scale: 1,
                    x: "0%"
                }
            }]
        },

        // kenburns-right
        {
            name : "kenburns-right",
            in : [{
                from : {
                    scale: 1.5,
                    x: "-10%"
                },
                to : {
                    scale: 1,
                    x: "0%"
                }
            }]
        },

        // kenburns-up-left
        {
            name : "kenburns-up-left",
            in : [{
                from : {
                    scale: 1.5,
                    x: "10%",
                    y: "10%"
                },
                to : {
                    scale: 1,
                    x: "0%",
                    y: "0%"
                }
            }]
        },

        // kenburns-up-right
        {
            name : "kenburns-up-right",
            in : [{
                from : {
                    scale: 1.5,
                    x: "-10%",
                    y: "10%"
                },
                to : {
                    scale: 1,
                    x: "0%",
                    y: "0%"
                }
            }]
        },

        // kenburns-down-left
        {
            name : "kenburns-down-left",
            in : [{
                from : {
                    scale: 1.5,
                    x: "10%",
                    y: "-10%"
                },
                to : {
                    scale: 1,
                    x: "0%",
                    y: "0%"
                }
            }]
        },

        // kenburns-down-right
        {
            name : "kenburns-down-right",
            in : [{
                from : {
                    scale: 1.5,
                    x: "-10%",
                    y: "-10%"
                },
                to : {
                    scale: 1,
                    x: "0%",
                    y: "0%"
                }
            }]
        },

        // fade-in-bg
        {
            template: "fade-screen",
            name : "fade-in-bg",
            type: "prior",
            alphaDuration: 0.001,
            in : [{
                duration: 0.001,
                from : {

                },
                to : {

                }
            }]
        },

        // none
        {
            template: "default",
            name : "none",
            type: "prior",
            alphaDuration: 0.001,
            in : [{
                duration: 0.001,
                from : {

                },
                to : {

                }
            }]
        },

        // zoom-in
        {
            alphaDuration: 6.5,
            name : "zoom-in",
            in : [{
                from : {
                    scale: 1
                },
                to : {
                    scale: 1.1
                }
            }]
        },

        // slide-left
        {
            name : "slide-left",
            in : [{
                from : {
                    x: "100%"
                },
                to : {
                    x: "0%"
                }
            }]
        },

        // slide-right
        {
            name : "slide-right",
            in : [{
                from : {
                    x: "-100%"
                },
                to : {
                    x: "0%"
                }
            }]
        },

        // slide-up
        {
            name : "slide-up",
            in : [{
                from : {
                    y: "100%"
                },
                to : {
                    y: "0%"
                }
            }]
        },

        // slide-down
        {
            name : "slide-down",
            in : [{
                from : {
                    y: "-100%"
                },
                to : {
                    y: "0%"
                }
            }]
        },

        // zoom-out
        {
            name : "zoom-out",
            in : [{
                from : {
                    scale: 1.3
                },
                to : {
                    scale: 1.1
                }
            }]
        },

        // swirl-left
        {
            name : "swirl-left",
            in : [{
                from : {
                    scale: 1.3,
                    rotation: 5
                },
                to : {
                    scale: 1.1,
                    rotation: 2
                }
            }]
        },

        // swirl-right
        {
            name : "swirl-right",
            in : [{
                from : {
                    scale: 1.3,
                    rotation: -5
                },
                to : {
                    scale: 1.1,
                    rotation: -2
                }
            }]
        }
    ]};
