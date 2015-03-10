(function(c) {
    var d = {
        version: 4.0
    };
    c.WeixinApi = d;
    if (typeof define === "function" && (define.amd || define.cmd)) {
        if (define.amd) {
            define(function() {
                return d
            })
        } else {
            if (define.cmd) {
                define(function(f, e, g) {
                    g.exports = d
                })
            }
        }
    }
    var b = function() {
        var f = {},
        j, g;
        for (var h = 0,
        e = arguments.length; h < e; h++) {
            j = arguments[h];
            if (typeof j === "object") {
                for (g in j) {
                    j[g] && (f[g] = j[g])
                }
            }
        }
        return f
    };
    var a = function(i, h, g) {
        g = g || {};
        var e = function(j) {
            switch (true) {
            case / \: cancel$ / i.test(j.err_msg) : g.cancel && g.cancel(j);
                break;
            case / \: (confirm | ok) $ / i.test(j.err_msg) : g.confirm && g.confirm(j);
                break;
            case / \: fail$ / i.test(j.err_msg) : default:
                g.fail && g.fail(j);
                break
            }
            g.all && g.all(j)
        };
        var f = function(k, j) {
            if (i.menu == "menu:share:timeline" || (i.menu == "general:share" && j.shareTo == "timeline")) {
                var l = k.title;
                k.title = k.desc || l;
                k.desc = l || k.desc
            }
            if (j && (j.shareTo == "favorite" || j.scene == "favorite")) {
                if (g.favorite === false) {
                    WeixinJSBridge.invoke("sendAppMessage", k, new Function())
                } else {
                    WeixinJSBridge.invoke(i.action, k, e)
                }
            } else {
                if (i.menu === "general:share") {
                    if (j.shareTo === "timeline") {
                        WeixinJSBridge.invoke("shareTimeline", k, e)
                    } else {
                        if (j.shareTo === "friend") {
                            WeixinJSBridge.invoke("sendAppMessage", k, e)
                        } else {
                            if (j.shareTo === "QQ") {
                                WeixinJSBridge.invoke("shareQQ", k, e)
                            } else {
                                if (j.shareTo === "weibo") {
                                    WeixinJSBridge.invoke("shareWeibo", k, e)
                                }
                            }
                        }
                    }
                } else {
                    WeixinJSBridge.invoke(i.action, k, e)
                }
            }
        };
        WeixinJSBridge.on(i.menu,
        function(k) {
            g.dataLoaded = g.dataLoaded || new Function();
            if (g.async && g.ready) {
                d._wx_loadedCb_ = g.dataLoaded;
                if (d._wx_loadedCb_.toString().indexOf("_wx_loadedCb_") > 0) {
                    d._wx_loadedCb_ = new Function()
                }
                g.dataLoaded = function(m) {
                    g.__cbkCalled = true;
                    var l = b(h, m);
                    l.img_url = l.imgUrl || l.img_url;
                    delete l.imgUrl;
                    d._wx_loadedCb_(l);
                    f(l, k)
                };
                if (! (k && (k.shareTo == "favorite" || k.scene == "favorite") && g.favorite === false)) {
                    g.ready && g.ready(k, h);
                    if (!g.__cbkCalled) {
                        g.dataLoaded({});
                        g.__cbkCalled = false
                    }
                }
            } else {
                var j = b(h);
                if (! (k && (k.shareTo == "favorite" || k.scene == "favorite") && g.favorite === false)) {
                    g.ready && g.ready(k, j)
                }
                f(j, k)
            }
        })
    };
    d.shareToTimeline = function(f, e) {
        a({
            menu: "menu:share:timeline",
            action: "shareTimeline"
        },
        {
            appid: f.appId ? f.appId: "",
            img_url: f.imgUrl,
            link: f.link,
            desc: f.desc,
            title: f.title,
            img_width: "640",
            img_height: "640"
        },
        e)
    };
    d.shareToFriend = function(f, e) {
        a({
            menu: "menu:share:appmessage",
            action: "sendAppMessage"
        },
        {
            appid: f.appId ? f.appId: "",
            img_url: f.imgUrl,
            link: f.link,
            desc: f.desc,
            title: f.title,
            img_width: "640",
            img_height: "640"
        },
        e)
    };
    d.shareToWeibo = function(f, e) {
        a({
            menu: "menu:share:weibo",
            action: "shareWeibo"
        },
        {
            content: f.desc,
            url: f.link
        },
        e)
    };
    d.generalShare = function(f, e) {
        a({
            menu: "general:share"
        },
        {
            appid: f.appId ? f.appId: "",
            img_url: f.imgUrl,
            link: f.link,
            desc: f.desc,
            title: f.title,
            img_width: "640",
            img_height: "640"
        },
        e)
    };
    d.disabledShare = function(e) {
        e = e ||
        function() {
            alert("当前页面禁止分享！")
        }; ["menu:share:timeline", "menu:share:appmessage", "menu:share:qq", "menu:share:weibo", "general:share"].forEach(function(f) {
            WeixinJSBridge.on(f,
            function() {
                e();
                return false
            })
        })
    };
    d.addContact = function(e, f) {
        f = f || {};
        WeixinJSBridge.invoke("addContact", {
            webtype: "1",
            username: e
        },
        function(h) {
            var g = !h.err_msg || "add_contact:ok" == h.err_msg || "add_contact:added" == h.err_msg;
            if (g) {
                f.success && f.success(h)
            } else {
                f.fail && f.fail(h)
            }
        })
    };
    d.imagePreview = function(e, f) {
        if (!e || !f || f.length == 0) {
            return
        }
        WeixinJSBridge.invoke("imagePreview", {
            current: e,
            urls: f
        })
    };
    d.showOptionMenu = function() {
        WeixinJSBridge.call("showOptionMenu")
    };
    d.hideOptionMenu = function() {
        WeixinJSBridge.call("hideOptionMenu")
    };
    d.showToolbar = function() {
        WeixinJSBridge.call("showToolbar")
    };
    d.hideToolbar = function() {
        WeixinJSBridge.call("hideToolbar")
    };
    d.getNetworkType = function(e) {
        if (e && typeof e == "function") {
            WeixinJSBridge.invoke("getNetworkType", {},
            function(f) {
                e(f.err_msg)
            })
        }
    };
    d.closeWindow = function(e) {
        e = e || {};
        WeixinJSBridge.invoke("closeWindow", {},
        function(f) {
            switch (f.err_msg) {
            case "close_window:ok":
                e.success && e.success(f);
                break;
            default:
                e.fail && e.fail(f);
                break
            }
        })
    };
    d.ready = function(h) {
        var f = function() {
            var i = {};
            Object.keys(WeixinJSBridge).forEach(function(j) {
                i[j] = WeixinJSBridge[j]
            });
            Object.keys(WeixinJSBridge).forEach(function(j) {
                if (typeof WeixinJSBridge[j] === "function") {
                    WeixinJSBridge[j] = function() {
                        try {
                            var k = arguments.length > 0 ? arguments[0] : {},
                            l = k.__params ? k.__params.__runOn3rd_apis || [] : []; ["menu:share:timeline", "menu:share:appmessage", "menu:share:weibo", "menu:share:qq", "general:share"].forEach(function(n) {
                                l.indexOf(n) === -1 && l.push(n)
                            })
                        } catch(m) {}
                        return i[j].apply(WeixinJSBridge, arguments)
                    }
                }
            })
        };
        if (h && typeof h == "function") {
            var e = this;
            var g = function() {
                f();
                h(e)
            };
            if (typeof c.WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener("WeixinJSBridgeReady", g, false)
                } else {
                    if (document.attachEvent) {
                        document.attachEvent("WeixinJSBridgeReady", g);
                        document.attachEvent("onWeixinJSBridgeReady", g)
                    }
                }
            } else {
                g()
            }
        }
    };
    d.openInWeixin = function() {
        return /MicroMessenger/i.test(navigator.userAgent)
    };
    d.scanQRCode = function(e) {
        e = e || {};
        WeixinJSBridge.invoke("scanQRCode", {
            needResult: e.needResult ? 1 : 0,
            desc: e.desc || "WeixinApi Desc",
            scanType: ["qrCode", "barCode"]
        },
        function(f) {
            switch (f.err_msg) {
            case "scanQRCode:ok":
            case "scan_qrcode:ok":
                e.success && e.success(f);
                break;
            default:
                e.fail && e.fail(f);
                break
            }
        })
    };
    d.getInstallState = function(f, e) {
        e = e || {};
        WeixinJSBridge.invoke("getInstallState", {
            packageUrl: f.packageUrl || "",
            packageName: f.packageName || ""
        },
        function(i) {
            var h = i.err_msg,
            g = h.match(/state:yes_?(.*)$/);
            if (g) {
                i.version = g[1] || "";
                e.success && e.success(i)
            } else {
                e.fail && e.fail(i)
            }
            e.all && e.all(i)
        })
    };
    d.sendEmail = function(f, e) {
        e = e || {};
        WeixinJSBridge.invoke("sendEmail", {
            title: f.subject,
            content: f.body
        },
        function(g) {
            if (g.err_msg === "send_email:sent") {
                e.success && e.success(g)
            } else {
                e.fail && e.fail(g)
            }
            e.all && e.all(g)
        })
    };
    d.enableDebugMode = function(e) {
        c.onerror = function(i, g, f, h) {
            if (typeof e === "function") {
                e({
                    message: i,
                    script: g,
                    line: f,
                    column: h
                })
            } else {
                var j = [];
                j.push("额，代码有错。。。");
                j.push("\n错误信息：", i);
                j.push("\n出错文件：", g);
                j.push("\n出错位置：", f + "行，" + h + "列");
                alert(j.join(""))
            }
        }
    }
})(window);