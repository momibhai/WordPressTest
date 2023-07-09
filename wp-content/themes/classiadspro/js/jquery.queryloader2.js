/*
 * QueryLoader v2 - A simple script to create a preloader for images
 *
 * For instructions read the original post:
 * http://www.gayadesign.com/diy/queryloader2-preload-your-images-with-ease/
 *
 * Copyright (c) 2011 - Gaya Kessler
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Version:  2.9.0
 * Last update: 2014-01-31
 */
! function (a) {
    function b(a) {
        this.parent = a, this.container, this.loadbar, this.percentageContainer, this.logo
    }

    function c(a) {
        this.toPreload = [], this.parent = a, this.container
    }

    function d(a) {
        this.element, this.parent = a
    }

    function e(d, e) {
        this.element = d, this.$element = a(d), this.options = e, this.foundUrls = [], this.destroyed = !1, this.imageCounter = 0, this.imageDone = 0, this.alreadyLoaded = !1, this.preloadContainer = new c(this), this.overlayLoader = new b(this), this.defaultOptions = {
            onComplete: function () {},
            onLoadComplete: function () {},
            backgroundColor: "#444",
            barColor: "#fff",
            overlayId: "qLoverlay",
            barHeight: 20,
            percentage: !1,
            deepSearch: !0,
            completeAnimation: "fade",
            minimumTime: 10000
        }, this.init()
    }! function (a) {
        "use strict";

        function b(b) {
            var c = a.event;
            return c.target = c.target || c.srcElement || b, c
        }
        var c = document.documentElement,
            d = function () {};
        c.addEventListener ? d = function (a, b, c) {
            a.addEventListener(b, c, !1)
        } : c.attachEvent && (d = function (a, c, d) {
            a[c + d] = d.handleEvent ? function () {
                var c = b(a);
                d.handleEvent.call(d, c)
            } : function () {
                var c = b(a);
                d.call(a, c)
            }, a.attachEvent("on" + c, a[c + d])
        });
        var e = function () {};
        c.removeEventListener ? e = function (a, b, c) {
            a.removeEventListener(b, c, !1)
        } : c.detachEvent && (e = function (a, b, c) {
            a.detachEvent("on" + b, a[b + c]);
            try {
                delete a[b + c]
            } catch (d) {
                a[b + c] = void 0
            }
        });
        var f = {
            bind: d,
            unbind: e
        };
        "function" == typeof define && define.amd ? define(f) : "object" == typeof exports ? module.exports = f : a.eventie = f
    }(this),
    function () {
        "use strict";

        function a() {}

        function b(a, b) {
            for (var c = a.length; c--;)
                if (a[c].listener === b) return c;
            return -1
        }

        function c(a) {
            return function () {
                return this[a].apply(this, arguments)
            }
        }
        var d = a.prototype,
            e = this,
            f = e.EventEmitter;
        d.getListeners = function (a) {
            var b, c, d = this._getEvents();
            if (a instanceof RegExp) {
                b = {};
                for (c in d) d.hasOwnProperty(c) && a.test(c) && (b[c] = d[c])
            } else b = d[a] || (d[a] = []);
            return b
        }, d.flattenListeners = function (a) {
            var b, c = [];
            for (b = 0; b < a.length; b += 1) c.push(a[b].listener);
            return c
        }, d.getListenersAsObject = function (a) {
            var b, c = this.getListeners(a);
            return c instanceof Array && (b = {}, b[a] = c), b || c
        }, d.addListener = function (a, c) {
            var d, e = this.getListenersAsObject(a),
                f = "object" == typeof c;
            for (d in e) e.hasOwnProperty(d) && -1 === b(e[d], c) && e[d].push(f ? c : {
                listener: c,
                once: !1
            });
            return this
        }, d.on = c("addListener"), d.addOnceListener = function (a, b) {
            return this.addListener(a, {
                listener: b,
                once: !0
            })
        }, d.once = c("addOnceListener"), d.defineEvent = function (a) {
            return this.getListeners(a), this
        }, d.defineEvents = function (a) {
            for (var b = 0; b < a.length; b += 1) this.defineEvent(a[b]);
            return this
        }, d.removeListener = function (a, c) {
            var d, e, f = this.getListenersAsObject(a);
            for (e in f) f.hasOwnProperty(e) && (d = b(f[e], c), -1 !== d && f[e].splice(d, 1));
            return this
        }, d.off = c("removeListener"), d.addListeners = function (a, b) {
            return this.manipulateListeners(!1, a, b)
        }, d.removeListeners = function (a, b) {
            return this.manipulateListeners(!0, a, b)
        }, d.manipulateListeners = function (a, b, c) {
            var d, e, f = a ? this.removeListener : this.addListener,
                g = a ? this.removeListeners : this.addListeners;
            if ("object" != typeof b || b instanceof RegExp)
                for (d = c.length; d--;) f.call(this, b, c[d]);
            else
                for (d in b) b.hasOwnProperty(d) && (e = b[d]) && ("function" == typeof e ? f.call(this, d, e) : g.call(this, d, e));
            return this
        }, d.removeEvent = function (a) {
            var b, c = typeof a,
                d = this._getEvents();
            if ("string" === c) delete d[a];
            else if (a instanceof RegExp)
                for (b in d) d.hasOwnProperty(b) && a.test(b) && delete d[b];
            else delete this._events;
            return this
        }, d.removeAllListeners = c("removeEvent"), d.emitEvent = function (a, b) {
            var c, d, e, f, g = this.getListenersAsObject(a);
            for (e in g)
                if (g.hasOwnProperty(e))
                    for (d = g[e].length; d--;) c = g[e][d], c.once === !0 && this.removeListener(a, c.listener), f = c.listener.apply(this, b || []), f === this._getOnceReturnValue() && this.removeListener(a, c.listener);
            return this
        }, d.trigger = c("emitEvent"), d.emit = function (a) {
            var b = Array.prototype.slice.call(arguments, 1);
            return this.emitEvent(a, b)
        }, d.setOnceReturnValue = function (a) {
            return this._onceReturnValue = a, this
        }, d._getOnceReturnValue = function () {
            return this.hasOwnProperty("_onceReturnValue") ? this._onceReturnValue : !0
        }, d._getEvents = function () {
            return this._events || (this._events = {})
        }, a.noConflict = function () {
            return e.EventEmitter = f, a
        }, "function" == typeof define && define.amd ? define(function () {
            return a
        }) : "object" == typeof module && module.exports ? module.exports = a : this.EventEmitter = a
    }.call(this),
    function (a, b) {
        "use strict";
        "function" == typeof define && define.amd ? define(["eventEmitter/EventEmitter", "eventie/eventie"], function (c, d) {
            return b(a, c, d)
        }) : "object" == typeof exports ? module.exports = b(a, require("eventEmitter"), require("eventie")) : a.imagesLoaded = b(a, a.EventEmitter, a.eventie)
    }(this, function (a, b, c) {
        "use strict";

        function d(a, b) {
            for (var c in b) a[c] = b[c];
            return a
        }

        function e(a) {
            return "[object Array]" === m.call(a)
        }

        function f(a) {
            var b = [];
            if (e(a)) b = a;
            else if ("number" == typeof a.length)
                for (var c = 0, d = a.length; d > c; c++) b.push(a[c]);
            else b.push(a);
            return b
        }

        function g(a, b, c) {
            if (!(this instanceof g)) return new g(a, b);
            "string" == typeof a && (a = document.querySelectorAll(a)), this.elements = f(a), this.options = d({}, this.options), "function" == typeof b ? c = b : d(this.options, b), c && this.on("always", c), this.getImages(), j && (this.jqDeferred = new j.Deferred);
            var e = this;
            setTimeout(function () {
                e.check()
            })
        }

        function h(a) {
            this.img = a
        }

        function i(a) {
            this.src = a, n[a] = this
        }
        var j = a.jQuery,
            k = a.console,
            l = "undefined" != typeof k,
            m = Object.prototype.toString;
        g.prototype = new b, g.prototype.options = {}, g.prototype.getImages = function () {
            this.images = [];
            for (var a = 0, b = this.elements.length; b > a; a++) {
                var c = this.elements[a];
                "IMG" === c.nodeName && this.addImage(c);
                for (var d = c.querySelectorAll("img"), e = 0, f = d.length; f > e; e++) {
                    var g = d[e];
                    this.addImage(g)
                }
            }
        }, g.prototype.addImage = function (a) {
            var b = new h(a);
            this.images.push(b)
        }, g.prototype.check = function () {
            function a(a) {
                return b.options.debug && l, b.progress(a), c++, c === d && b.complete(), !0
            }
            var b = this,
                c = 0,
                d = this.images.length;
            if (this.hasAnyBroken = !1, !d) return this.complete(), void 0;
            for (var e = 0; d > e; e++) {
                var f = this.images[e];
                f.on("confirm", a), f.check()
            }
        }, g.prototype.progress = function (a) {
            this.hasAnyBroken = this.hasAnyBroken || !a.isLoaded;
            var b = this;
            setTimeout(function () {
                b.emit("progress", b, a), b.jqDeferred && b.jqDeferred.notify && b.jqDeferred.notify(b, a)
            })
        }, g.prototype.complete = function () {
            var a = this.hasAnyBroken ? "fail" : "done";
            this.isComplete = !0;
            var b = this;
            setTimeout(function () {
                if (b.emit(a, b), b.emit("always", b), b.jqDeferred) {
                    var c = b.hasAnyBroken ? "reject" : "resolve";
                    b.jqDeferred[c](b)
                }
            })
        }, j && (j.fn.imagesLoaded = function (a, b) {
            var c = new g(this, a, b);
            return c.jqDeferred.promise(j(this))
        }), h.prototype = new b, h.prototype.check = function () {
            var a = n[this.img.src] || new i(this.img.src);
            if (a.isConfirmed) return this.confirm(a.isLoaded, "cached was confirmed"), void 0;
            if (this.img.complete && void 0 !== this.img.naturalWidth) return this.confirm(0 !== this.img.naturalWidth, "naturalWidth"), void 0;
            var b = this;
            a.on("confirm", function (a, c) {
                return b.confirm(a.isLoaded, c), !0
            }), a.check()
        }, h.prototype.confirm = function (a, b) {
            this.isLoaded = a, this.emit("confirm", this, b)
        };
        var n = {};
        return i.prototype = new b, i.prototype.check = function () {
            if (!this.isChecked) {
                var a = new Image;
                c.bind(a, "load", this), c.bind(a, "error", this), a.src = this.src, this.isChecked = !0
            }
        }, i.prototype.handleEvent = function (a) {
            var b = "on" + a.type;
            this[b] && this[b](a)
        }, i.prototype.onload = function (a) {
            this.confirm(!0, "onload"), this.unbindProxyEvents(a)
        }, i.prototype.onerror = function (a) {
            this.confirm(!1, "onerror"), this.unbindProxyEvents(a)
        }, i.prototype.confirm = function (a, b) {
            this.isConfirmed = !0, this.isLoaded = a, this.emit("confirm", this, b)
        }, i.prototype.unbindProxyEvents = function (a) {
            c.unbind(a.target, "load", this), c.unbind(a.target, "error", this)
        }, g
    }), b.prototype.createOverlay = function () {
        var b = "absolute";
        if ("body" == this.parent.element.tagName.toLowerCase()) b = "fixed";
        else {
            var c = this.parent.$element.css("position");
            ("fixed" != c || "absolute" != c) && this.parent.$element.css("position", "relative")
        }
		
        /*this.container = a("<div id='" + this.parent.options.overlayId + "'><div  class='qLlogo'><img src="+this.parent.options.logo+"></img></div></div>").css({
            width: "100%",
            height: "100%",
            backgroundColor: this.parent.options.backgroundColor,
            backgroundPosition: "fixed",
            position: b,
            zIndex: 666999,
            top: 0,
            left: 0
        })*/.appendTo(this.parent.$element), this.loadbar = a("<div id='qLbar'></div>").css({
            height: this.parent.options.barHeight + "px",
            marginTop: "-" + this.parent.options.barHeight / 2 + "px",
            backgroundColor: this.parent.options.barColor,
            width: "0%",
            position: "absolute",
            top: "50%"
        }).appendTo(this.container), 1 == this.parent.options.percentage && (this.percentageContainer = a("<div id='qLpercentage'></div>").text("0%").css({
            height: "50px",
            width: "130px",
            position: "absolute",
            fontSize: "50px",
            fontWeight: "300",
            top: "50%",
            left: "50%",
            marginTop: "-" + (59 + this.parent.options.barHeight) + "px",
            textAlign: "center",
            marginLeft: "-60px",
            color: this.parent.options.textColor
        }).appendTo(this.container)), this.parent.preloadContainer.toPreload.length && 1 != this.parent.alreadyLoaded || this.parent.destroyContainers()
    }, b.prototype.updatePercentage = function (a) {
        this.loadbar.stop().animate({
            width: a + "%",
            minWidth: a + "%"
        }, 200), 1 == this.parent.options.percentage && this.percentageContainer.text(Math.ceil(a) + "%")
    }, c.prototype.create = function () {
        this.container = a("<div></div>").appendTo("body").css({
            display: "none",
            width: 0,
            height: 0,
            overflow: "hidden"
        }), this.processQueue()
    }, c.prototype.processQueue = function () {
        for (var a = 0; this.toPreload.length > a; a++) this.parent.destroyed || this.preloadImage(this.toPreload[a])
    }, c.prototype.addImage = function (a) {
        this.toPreload.push(a)
    }, c.prototype.preloadImage = function (a) {
        var b = new d;
        b.addToPreloader(this, a), b.bindLoadEvent()
    }, d.prototype.addToPreloader = function (b, c) {
        this.element = a("<img />").attr("src", c), this.element.appendTo(b.container), this.parent = b.parent
    }, d.prototype.bindLoadEvent = function () {
        this.parent.imageCounter++, this.element[0].ref = this, new imagesLoaded(this.element, function (a) {
            a.elements[0].ref.completeLoading()
        })
    }, d.prototype.completeLoading = function () {
        this.parent.imageDone++;
        var a = 100 * (this.parent.imageDone / this.parent.imageCounter);
        this.parent.overlayLoader.updatePercentage(a), (this.parent.imageDone == this.parent.imageCounter || a >= 100) && this.parent.endLoader()
    }, e.prototype.init = function () {
        if (this.options = a.extend({}, this.defaultOptions, this.options), this.findImageInElement(this.element), 1 == this.options.deepSearch)
            for (var b = this.$element.find("*:not(script)"), c = 0; c < b.length; c++) this.findImageInElement(b[c]);
        this.preloadContainer.create(), this.overlayLoader.createOverlay()
    }, e.prototype.findImageInElement = function (b) {
        var c = "",
            e = a(b),
            f = "normal";
        if ("none" != e.css("background-image") ? (c = e.css("background-image"), f = "background") : "undefined" != typeof e.attr("src") && "img" == b.nodeName.toLowerCase() && (c = e.attr("src")), !this.hasGradient(c)) {
            c = this.stripUrl(c);
            for (var g = c.split(", "), h = 0; h < g.length; h++)
                if (this.validUrl(g[h]) && this.urlIsNew(g[h])) {
                    var i = "";
                    if (this.isIE() || this.isOpera()) i = "?rand=" + Math.random(), this.preloadContainer.addImage(g[h] + i);
                    else if ("background" == f) this.preloadContainer.addImage(g[h] + i);
                    else {
                        var j = new d(this);
                        j.element = e, j.bindLoadEvent()
                    }
                    this.foundUrls.push(g[h])
                }
        }
    }, e.prototype.hasGradient = function (a) {
        return -1 == a.indexOf("gradient") ? !1 : !0
    }, e.prototype.stripUrl = function (a) {
        return a = a.replace(/url\(\"/g, ""), a = a.replace(/url\(/g, ""), a = a.replace(/\"\)/g, ""), a = a.replace(/\)/g, "")
    }, e.prototype.isIE = function () {
        return navigator.userAgent.match(/msie/i)
    }, e.prototype.isOpera = function () {
        return navigator.userAgent.match(/Opera/i)
    }, e.prototype.validUrl = function (a) {
        return a.length > 0 && !a.match(/^(data:)/i) ? !0 : !1
    }, e.prototype.urlIsNew = function (a) {
        return -1 == this.foundUrls.indexOf(a) ? !0 : !1
    }, e.prototype.destroyContainers = function () {
        this.destroyed = !0, this.preloadContainer.container.remove(), this.overlayLoader.container.remove()
    }, e.prototype.endLoader = function () {
        this.destroyed = !0, this.onLoadComplete()
    }, e.prototype.onLoadComplete = function () {
        if (this.options.onLoadComplete(), "grow" == this.options.completeAnimation) {
            var b = this.options.minimumTime;
            this.overlayLoader.loadbar[0].parent = this, this.overlayLoader.loadbar.stop().animate({
                width: "100%"
            }, b, function () {
                a(this).animate({
                    top: "0%",
                    width: "100%",
                    height: "100%"
                }, 500, function () {
                    this.parent.overlayLoader.container[0].parent = this.parent, this.parent.overlayLoader.container.fadeOut(500, function () {
                        this.parent.destroyContainers(), this.parent.options.onComplete()
                    })
                })
            })
        } else {
            var b = this.options.minimumTime;
            this.overlayLoader.container[0].parent = this, this.overlayLoader.container.fadeOut(b, function () {
                this.parent.destroyContainers(), this.parent.options.onComplete()
            })
        }
    }, Array.prototype.indexOf || (Array.prototype.indexOf = function (a) {
        var b = this.length >>> 0,
            c = Number(arguments[1]) || 0;
        for (c = 0 > c ? Math.ceil(c) : Math.floor(c), 0 > c && (c += b); b > c; c++)
            if (c in this && this[c] === a) return c;
        return -1
    }), a.fn.queryLoader2 = function (a) {
        return this.each(function () {
            new e(this, a)
        })
    }
}(jQuery);