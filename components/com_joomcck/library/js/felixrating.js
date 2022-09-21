felixRating = {
    newRating: function (rating_box_id, active) {
        if (typeof document.getElementById(rating_box_id).ratingLoaded != "undefined") {
            var ratingWorkObject = {
                setStars: function (stars) {
                },
                setCurrentStar: function (current) {
                },
                setSedingFunction: function (func, ident) {
                },
                setIndex: function (index) {
                },
                rateUp: function (event) {
                },
                starTurnOn: function (event) {
                },
                starTurnOff: function (event) {
                },
                showState: function (reffObj, label, last) {
                }
            };
            return ratingWorkObject;
        }

        var ratingWorkObject = {
            active: active,
            ident: '',
            rated: false,
            rating_box: document.getElementById(rating_box_id),
            //mesage_box : document.getElementById(mesage_box_id),
            current: '',
            stars: Array(),
            sendingFunction: null,

            setStars: function (stars) {
                this.rating_box.ratingLoaded = true;

                for (star in stars) {
                    var newStar = document.createElement('span');
                    newStar.starNum = star;
                    newStar.setAttribute('rel', 'tooltip');
                    newStar.setAttribute('data-original-title', stars[star]);
                    newStar.reffObj = this;
                    if (this.active) {
                        if (newStar.addEventListener && navigator.appName != 'Opera') {
                            newStar.addEventListener('click', this.rateUp, true);
                            newStar.addEventListener('mouseover', this.starTurnOn, true);
                            newStar.addEventListener('mouseout', this.starTurnOff, true);
                        } else {
                            newStar.attachEvent('onclick', this.rateUp);
                            newStar.attachEvent('onmouseover', this.starTurnOn);
                            newStar.attachEvent('onmouseout', this.starTurnOff);
                        }
                    }
                    this.rating_box.appendChild(newStar);
                    this.stars[star] = newStar;

                }
            },

            setCurrentStar: function (current) {
                if (current > 0) {
                    var prev = '';
                    var nearest_current = '';

                    for (star in this.stars) {
                        if (isNaN(parseInt(star))) {
                            continue;
                        }
                        if ((prev == '' && current * 1 < this.stars[star].starNum * 1)
                            || current * 1 == this.stars[star].starNum * 1) {
                            nearest_current = this.stars[star].starNum;
                            break;
                        }

                        if (current * 1 > prev * 1
                            && current * 1 < this.stars[star].starNum * 1) {

                            var diff = this.stars[star].starNum - prev;
                            if (current - prev > diff / 2) {
                                nearest_current = this.stars[star].starNum;
                            } else {
                                nearest_current = prev;
                            }
                            break;
                        }

                        prev = this.stars[star].starNum;
                    }

                    this.current = '' + nearest_current;

                    this.showState(this, this.stars[this.current].title,
                        this.current);
                } else {

                    this.current = false;
                    this.showState(this, '', this.current);
                }

            },

            setSedingFunction: function (func, ident) {
                this.sendingFunction = func;
                this.ident = ident;
            },

            rateUp: function (event) {
                var target = event.target ? event.target : event.srcElement ? event.srcElement : '';
                if (!target.reffObj.rated && target.reffObj.sendingFunction) {
                    target.reffObj.showState(target.reffObj, target.title, target.starNum);
                    if(target.reffObj < 2)
                    {
                    	target.reffObj.rated = true;
                    }
                    target.reffObj.sendingFunction(target.starNum, target.reffObj.ident, target.reffObj.index);
                }
            },

            setIndex: function (index) {
                this.index = index;
            },

            starTurnOn: function (event) {
                var target = event.target ? event.target : event.srcElement ? event.srcElement : '';

                if (!target.reffObj.rated && target.reffObj.sendingFunction) {
                    target.reffObj.showState(target.reffObj, target.title, target.starNum);
                }

            },

            starTurnOff: function (event) {
                var target = event.target ? event.target : event.srcElement ? event.srcElement : '';

                if (!target.reffObj.rated && target.reffObj.sendingFunction) {

                    if (target.reffObj.current) {
                        target.reffObj.showState(
                            target.reffObj,
                            target.reffObj.stars[target.reffObj.current].title,
                            target.reffObj.current
                        );
                    } else {
                        target.reffObj.showState(target.reffObj, '', '');
                        // target.reffObj.mesage_box.innerHTML = '';
                        //target.reffObj.mesage_box.childNodes[0].nodeValue = ' ';
                    }
                }
            },

            showState: function (reffObj, label, last) {
                p = 0;
                for (star in reffObj.stars) {
                    if (isNaN(parseInt(star))) {
                        continue;
                    }
                    if (p == 0 && last) {
                        reffObj.stars[star].className = "on";

                        if (last == star) {
                            p = 1;
                            //reffObj.mesage_box.childNodes[0].nodeValue = label;
                            /* reffObj.mesage_box.innerHTML = label; */
                        }

                    } else {
                        reffObj.stars[star].className = "";
                    }
                }
            }
        }
        return ratingWorkObject;
    }
}
