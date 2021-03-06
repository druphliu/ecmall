if (!"".trim) {
    String.prototype.trim = function () {
        return this.replace(/^(\s|\u00A0)+|(\s|\u00A0)+$/g, "")
    }
}
$.extend({userAgent: window.USERAGENT || navigator.userAgent.toLowerCase(), jsonHandle: function (a) {
    if (a.msg) {
        alert(a.msg)
    }
    if (a.url) {
        location.href = a.url
    } else {
        if (a.refresh) {
            location.replace(location.href)
        }
    }
}, allpass: function (a) {
    var b = true;
    a = a || $("input");
    a.each(function () {
        var f = "", d = false, g = "", h = this.id, c = null, e = this.value.trim();
        if (b === true && h && (($(this).attr("required") === "" && e === "") || (d = (f = $(this).attr("pattern")) && !new RegExp(f).test(e))) && (c = $("label[for='" + h + "']")).length) {
            g = c.text().replace(/\*|:|：|\s/g, "");
            alert(d ? "您输入的" + g + "格式不准确。" : "您尚未输入" + g + "。");
            b = false;
            this.focus()
        }
    });
    return b
}, form: function (a, b) {
    $.overlay.show();
    $.ajax({url: a.action, type: a.method || "POST", data: $(a).serialize(), dataType: "json", success: function (c) {
        $.overlay.hide();
        $.jsonHandle(c);
        if (typeof b === "function") {
            b.call(a, c)
        }
    }, error: function () {
        $.overlay.hide();
        alert("由于网络的原因，您刚才的操作没有成功。")
    }})
}, ajaxFn: function (b, e, d, c, a) {
    d = d || "a=1";
    if (a != false) {
        $.overlay.show()
    }
    $.ajax({url: e, type: "POST", data: d, dataType: "json", success: function (f) {
        $.overlay.hide();
        $.jsonHandle(f);
        if (typeof c === "function") {
            c.call(b, f)
        }
    }, error: function () {
        $.overlay.hide();
        alert("由于网络的原因，您刚才的操作没有成功。")
    }})
}, overlay: {show: function () {
    var b = $("#overlay"), a = document.body.scrollHeight;
    if (b.length === 1) {
        b.show().css("height", a + "px")
    } else {
        $("body").append($('<div id="overlay" class="overlay" style="height:' + a + 'px;"></div>'))
    }
}, hide: function () {
    $("#overlay").remove()
}}, lazyload: function (b, c) {
    var a = [], c = c || function () {
    };
    if (/symbianos/.test($.userAgent)) {
        b.each(function () {
            this.src = $(this).attr("data-url");
            c.call(this)
        });
        return false
    }
    b.each(function () {
        var f = this.tagName.toLowerCase(), e = $(this).attr("data-url");
        a.push({ele: this, tag: f, url: e})
    });
    var d = function () {
        var f = $.scrollTop(), e = $.clientHeight();
        $.each(a, function (g, i) {
            if (!i.ele) {
                return
            }
            var h = $(i.ele).offset();
            if (h.top > f - h.height && h.top < f + e) {
                if (i.url && i.tag === "img") {
                    i.ele.src = i.url;
                    c.call(i.ele)
                } else {
                    c.call(i.ele)
                }
                i.ele = null
            }
        })
    };
    d();
    setTimeout(d, 500);
    $(window).bind("scroll", d)
}, tabSwitch: function (a, b, d) {
    b = b || "aRandomClass";
    var c = null;
    a.each(function (e) {
        if ($(this).hasClass(b)) {
            c = $(this)
        }
        $(this).bind("click", function () {
            if ($(this).hasClass(b) === false) {
                var f = $("#" + $(this).attr("data-rel"));
                $(this).addClass(b);
                f.show();
                if (c) {
                    c.removeClass(b);
                    $("#" + c.attr("data-rel")).hide()
                }
                if ($.isFunction(d)) {
                    d.call(this, f, c)
                }
                c = $(this)
            }
            return false
        })
    })
}, storage: {tel: function (b) {
    var c = "xmsTel", d = b.val(), a = null;
    if (window.localStorage && b.length) {
        if (d === "") {
            if (a = localStorage.getItem(c)) {
                $('<a href="javascript:" class="ico ico_close g9 abs" style="margin:.5em 0 0;">x</a>').bind("click",function () {
                    b.val("");
                    localStorage.removeItem(c);
                    $(this).remove();
                    return false
                }).insertAfter(b.val(a))
            }
        } else {
            localStorage.setItem(c, d)
        }
    }
}, location: function (d) {
    var a = "xmsLocal", c = "|", b = null;
    if (window.localStorage) {
        if (d instanceof Array) {
            d.push(new Date().getTime());
            localStorage.setItem(a, d.join(c))
        } else {
            return(b = localStorage.getItem(a)) ? b.split(c) : b
        }
    } else {
        return null
    }
}, relocation: function (c) {
    var b = "xmsReLocal", d = "|", a = null;
    if (window.sessionStorage) {
        if (c == true) {
            sessionStorage.setItem(b, c)
        } else {
            return(a = sessionStorage.getItem(b)) ? a.split(d) : a
        }
    } else {
        return null
    }
}}, ajaxLocation: "ajax/location.asp", location: function (h, g, f) {
    if (!$.isFunction(g)) {
        g = function () {
        }
    }
    if (!$.isFunction(h)) {
        h = function () {
        }
    }
    var d = $.storage.location(), j = new Date().getTime();
    if (f !== true && d && d[2] && (j - d[2] < 10 * 60000)) {
        return
    }
    var c = function (m, k) {
        var l = m && k;
        $.ajax({url: $.ajaxLocation, data: {latitude: m || -1, longitude: k || -1}, dataType: "json", success: function (n) {
            l ? h(n) : g(n)
        }, error: function () {
            alert("由于网络或设备的原因，定位出现异常。");
            g()
        }})
    };
    var e = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (k) {
                var m = k.coords.latitude, l = k.coords.longitude;
                if (m && l) {
                    $.storage.location([m, l]);
                    c(m, l)
                }
            }, function () {
                c()
            })
        } else {
            c()
        }
    };
    var a = location.href, b = /[^\w]m1\./g;
    if (b.test(a)) {
        var i = (function () {
            var l = {enableHighAccuracy: true, maximumAge: 5000, timeout: 5000};
            var q = false;
            var r = "lbxr4kuSulEiUjkkTeZKkspQ";
            clouda.lightapp(r, function () {
                q = true
            });
            var p = function (t) {
                lng = t.longitude;
                lat = t.latitude;
                c(lat, lng)
            };
            var k = function (t) {
                c()
            };

            function m() {
                if (navigator.userAgent.indexOf("Android") > -1) {
                    if (navigator.userAgent.indexOf("baiduboxapp") > -1) {
                        if (q) {
                            clouda.device.geolocation.get({onsuccess: p, onfail: k})
                        }
                    } else {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(s, n, l)
                        }
                    }
                } else {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(s, o, l)
                    }
                }
            }

            function n() {
                if (q) {
                    clouda.lightapp(r, function () {
                        clouda.device.geolocation.get({onsuccess: p, onfail: o})
                    })
                }
            }

            function s(t) {
                var v = t.coords.latitude;
                var u = t.coords.longitude;
                c(v, u)
            }

            function o() {
                alert("定位失败")
            }

            return m
        })();
        setTimeout(function () {
            i()
        }, 300)
    } else {
        e()
    }
}, moreToggle: function (b, c) {
    c = c || false;
    var a = c;
    b.each(function () {
        $(this).data("inittext", $(this).html()).bind("click", function () {
            var e = $("#" + $(this).attr("data-rel")), d = $(this).data("inittext");
            if (c === false) {
                e.css("display", "inline");
                $(this).html(a ? d : "&laquo; 收起更多");
                c = true
            } else {
                e.hide();
                $(this).html(a ? "查看更多 &raquo;" : d);
                c = false
            }
        })
    })
}, relocation: function (b) {
    var c = (typeof b == "string") ? JSON.parse(b) : b, a = $.storage.relocation();
    if (c.succ == false && !a && c.msg != "") {
        if (confirm(c.msg)) {
            location.href = c.url
        } else {
            $.storage.relocation(true)
        }
    }
}});
(function () {
    if (window.NOFONTFACE) {
        $(".ico").each(function () {
            if (!this.ico && $(this).html() === "") {
                $(this).hide()
            }
        })
    }
    var b = null, a = $("#header").size();
    $("#nav a").bind("touchstart", function () {
        if (!/nav_on/.test(this.className)) {
            b = $(this).css("background-color", a ? "rgba(0,0,0,.1)" : "rgba(255,0,0,.7)")
        }
    });
    $(document.body).bind("touchend", function (c) {
        if (c.target && /nav_a/.test(c.target.className)) {
            return
        }
        b && b.css("background-color", a ? "rgba(0,0,0,.3)" : "rgba(255,0,0,.5)")
    })
})();
var changewidth = function () {
    var e = $("#appLoad"), b = $("#appClose"), a = b.data("href"), c = $("#apploadbg");
    if (e.length > 0) {
        e.show();
        b.bind("click", function () {
            $.ajaxFn($(this), a, "");
            e.hide();
            return false
        });
        var d = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        if (document.addEventListener) {
            document.addEventListener("touchend", function () {
                setTimeout(function () {
                    e.css("width", d + "px");
                    c.css("width", d + "px")
                }, 50);
                setTimeout(function () {
                    e.css("width", "100%");
                    c.css("width", "100%")
                }, 200)
            })
        }
    }
};
var xmsCommentSend = function () {
    var f = $("#totalStar"), e = $("#commArea"), d = $("#tasteScore").html();
    $("select").bind("change", function () {
        var h = $(this).val(), g = $(this).attr("data-rel");
        if ($(this).attr("id") === "selectTotal") {
            f.css("width", (h * 20) + "%")
        }
        if (g === "totalScore") {
            $("#" + g).css("fontSize", "16px").removeClass("g9").addClass("co").html(h + ".0")
        } else {
            if (h && h !== "0") {
                $("#" + g).html(h + ".0")
            } else {
                $("#" + g).html(d)
            }
        }
    });
    var c = $("#totalScore");
    c.css("fontSize", "14px").html(c.attr("data-text"));
    var b = false, a = {total: "总体", taste: "口味", envir: "环境", server: "服务"};
    $("#commForm").bind("submit", function () {
        b = false;
        $("select").each(function () {
            if (b === false && this.value === "") {
                alert("您尚未选择" + a[$(this).attr("data-rel").replace("Score", "")] + "评分。");
                b = true
            }
        });
        var g = "";
        if (b === false) {
            if ((!(g = e.val()) || g.length < e.attr("data-min"))) {
                alert(e.attr("placeholder"));
                if (g.length === 0) {
                    e.get(0).focus()
                }
            } else {
                $.form(this)
            }
        }
        return false
    })
};
var xmsSearch = function () {
    var h = $("#searchForm");
    if (h.length === 0) {
        return
    }
    var e = h.get(0).getElementsByTagName("select"), a = e.length;
    var d = function (p) {
        var n = p.get(0).getElementsByTagName("option"), l = n.length;
        var o = $("#" + p.attr("data-rel"));
        for (var k = 0; k < n.length; k += 1) {
            if (n[k].selected) {
                var m = o.html($(n[k]).html());
                var i = $(n[k]).attr("data-name");
                if (i) {
                    p.attr("name") = i
                }
            }
        }
    };
    for (var c = 0; c < a; c += 1) {
        $(e[c]).bind("change", function () {
            d($(this));
            h.get(0).submit()
        });
        d($(e[c]))
    }
    var j = $("#searchS"), b = j.length && j.get(0).getElementsByTagName("input")[0], f = $("#searchIcoBtn"), g = false;
    f.bind("click", function () {
        if (g === false) {
            j.show();
            g = true
        } else {
            j.hide();
            g = false
        }
        return false
    });
    if (b && b.value) {
        f.trigger("click")
    }
};
var xmsBook = function () {
    var b = $("#dinnerTel");
    $("#bookFillForm").bind("submit", function (h) {
        if ($.allpass()) {
            $.form(this)
        }
        h.preventDefault();
        return false
    });
    $("input[type='radio']").each(function () {
        var h = $("label[for='" + this.id + "']"), i = $(this);
        h.bind("click", function () {
            if (!i.attr("checked")) {
                i.attr("checked", "checked")
            }
        })
    });
    var a = $("#dinnerBawc"), g = a.html();
    $("#bookFillBawc a").bind("click", function () {
        var h = $(this).find("img"), j = $(this).find("name"), i = $(this).attr("data-mini");
        if (h.hasClass("on")) {
            a.hide().html(g.replace("$id$", ""));
            h.removeClass("on")
        } else {
            $("img.on").removeClass("on");
            h.addClass("on");
            a.html(g.replace("$id$", $(this).attr("data-id")).replace("$name$", j.html() + (i ? "（最低消费" + i + "）" : ""))).show()
        }
    });
    var e = $("#bookFillForm");
    if (e.length === 0) {
        return
    }
    var d = e.get(0).getElementsByTagName("select"), f = d.length;
    for (var c = 0; c < f; c += 1) {
        $(d[c]).bind("change", function () {
            var m = $(this), l = m.get(0).getElementsByTagName("option"), k = l.length;
            for (var i = 0; i < l.length; i += 1) {
                if (l[i].selected) {
                    var h = $(l[i]).val();
                    if (h) {
                        m.closest("select").siblings("label").html(h)
                    }
                }
            }
        })
    }
};
var xmsCashMibiWaimai = function (g, j, h) {
    var n = "", e = "", b = "", m = "", s = $("#tatalPrice"), i = $("#tatalDish"), k = $("#menuPrice"), l = k.data("text"), c = l, q = {}, o = "";
    if (localStorage.getItem("resid") == "" || localStorage.getItem("resid") != g) {
        localStorage.setItem("resid", g);
        localStorage.removeItem("num");
        localStorage.removeItem("price");
        localStorage.removeItem("dish")
    }
    var f = function (w, t) {
        var u = w.data("name"), z = parseInt(w.data("price")), y = 0, B = w.data("dishName"), x = w.data("img"), C = w.data("unit");
        e = parseInt(w.html(), 10);
        if (e < 0) {
            e = 0;
            w.html(e)
        } else {
            if (window.localStorage) {
                w.html(t);
                o = JSON.parse(localStorage.getItem("dish"));
                if (localStorage.getItem("dish") == null) {
                    q = '[{"name":"' + u + '","dishName":"' + B + '","img":"' + x + '","unit":"' + C + '","num":"1","price":"' + z + '"}]';
                    localStorage.setItem("num", 1);
                    localStorage.setItem("price", z);
                    localStorage.setItem("dish", q);
                    y = p(s, "+", z)
                } else {
                    for (var v = 0, A = o.length; v < A; v++) {
                        if (o[v] != null && o[v].name == u) {
                            if (o[v].num > t) {
                                y = p(s, "-", z);
                                if (t === 0) {
                                    id = w.data("id");
                                    if (j === "2") {
                                        $("#" + id).find(".addNum").show();
                                        $("#" + id).find(".sex_box").hide()
                                    } else {
                                        if (j === "1") {
                                            $("#" + id).remove()
                                        }
                                    }
                                    delete o[v]
                                } else {
                                    o[v].num = o[v].num * 1 - 1 + ""
                                }
                                localStorage.setItem("price", y);
                                localStorage.setItem("num", localStorage.getItem("num") != 0 ? (localStorage.getItem("num") * 1 - 1) : 0);
                                localStorage.setItem("dish", JSON.stringify(o))
                            } else {
                                localStorage.setItem("num", localStorage.getItem("num") * 1 + 1);
                                o[v].num = o[v].num * 1 + 1 + "";
                                y = p(s, "+", z);
                                localStorage.setItem("price", y);
                                localStorage.setItem("dish", JSON.stringify(o))
                            }
                            break
                        } else {
                            if (v == o.length - 1) {
                                q = localStorage.getItem("dish").slice(0, localStorage.getItem("dish").length - 1) + ',{"name":"' + u + '","dishName":"' + B + '","img":"' + x + '","unit":"' + C + '","num":"1","price":"' + z + '"}]';
                                localStorage.setItem("num", localStorage.getItem("num") * 1 + 1);
                                localStorage.setItem("dish", q);
                                y = p(s, "+", z);
                                localStorage.setItem("price", y)
                            }
                        }
                    }
                }
                d(i, localStorage.getItem("num"))
            }
        }
        r(k, y, c)
    };
    var r = function (x, u, v) {
        if (x.length != "-1") {
            var w = x.data("price"), t = w - u;
            if (w === "-1") {
                if (u > 0) {
                    x.attr("data-true", "true");
                    x.removeAttr("disabled");
                    x.removeClass("order_false")
                } else {
                    x.attr({"data-true": "false", disabled: "disabled"});
                    x.addClass("order_false")
                }
            } else {
                if (u < w) {
                    x.attr({"data-true": "false", disabled: "disabled"});
                    x.addClass("order_false");
                    l = "还差￥" + t + "元起送"
                } else {
                    x.attr("data-true", "true");
                    x.removeAttr("disabled");
                    x.removeClass("order_false");
                    l = v
                }
            }
            x.val(l)
        }
    };
    var d = function (t, u) {
        if (u < 0) {
            u = 0
        }
        if (t.attr("type") == "text") {
            t.val(u)
        } else {
            t.html(u)
        }
    };
    var p = function (v, t, u) {
        if (v.attr("type") == "text") {
            if (t == "+") {
                Amount = v.val() * 1 + u * 1
            } else {
                Amount = v.val() * 1 - u
            }
            v.val(Amount)
        } else {
            if (t == "+") {
                Amount = v.html() * 1 + u * 1
            } else {
                Amount = v.html() * 1 - u
            }
            v.html(Amount)
        }
        if (Amount < 0) {
            Amount = 0
        }
        return Amount
    };
    $("body").delegate(".cashNum", "click", function () {
        var u = $(this), t = u.data("type"), v = 0;
        m = u.siblings(".cashNumShow");
        b = m.html().trim();
        if (t == "+") {
            v = Number(m.html().trim()) + 1
        } else {
            if (t == "-") {
                v = Number(m.html().trim()) - 1
            }
        }
        f(m, v)
    });
    $(".addNum").bind("click", function () {
        $(this).hide().siblings(".sex_box").show();
        var t = $(this).siblings(".sex_box").find(".cashNumShow");
        f(t, 1)
    });
    var a = function () {
        var z = $("#menuPrice"), y = "", u = "", A = "", w = localStorage.getItem("price");
        var v = function () {
            if (z.data("price") * 1 !== -1 && z.data("price") * 1 > w * 1) {
                z.val("还差￥" + (w != 0 ? (z.data("price") * 1 - w) : z.data("price")) + "元起送");
                z.attr({"data-true": "false", disabled: "disabled"});
                z.addClass("order_false")
            } else {
                if (z.data("price") * 1 === -1 && localStorage.getItem("price") * 1 === 0) {
                    z.val("去买单");
                    z.attr({"data-true": "false", disabled: "disabled"});
                    z.addClass("order_false")
                } else {
                    z.val("去买单");
                    z.attr({"data-true": "true"});
                    z.removeClass("order_false")
                }
            }
        };
        var t = function (F, H) {
            var D = $(F).serialize(), G = D.split("&"), C = [];
            for (var E = 0, B = G.length; E < B; E++) {
                C = G[E].split("=");
                H[C[0]] = C[1]
            }
            return H
        };
        var x = function () {
            var G = "", C = "", E = "", F = $("#orderDishList");
            G = JSON.parse(localStorage.getItem("dish"));
            if (G) {
                for (var D = 0, B = G.length; D < B; D++) {
                    if (G[D] != null && G[D].name) {
                        if ($("#menuPrice").hasClass("orderDish")) {
                            E = '<li id="m' + G[D].name + '"><div class="ovh"><img class="l" src="' + G[D].img + '" width="55" height="55" alt="' + G[D].dishName + '"><div class="pl10 well"><h2 class="pb10 pw220 ell">' + G[D].dishName + '</h2><span class="l g9">￥' + G[D].price + G[D].unit + '</span><span id="m1" class="r numbox addNum" style="display: none;" id="m' + G[D].name + '">+</span><div class="r sex_box" style="display:block;"><div class="num"><span class="l cashNum" data-type="-">－</span><span class="l cash_n cashNumShow" data-price="' + G[D].price + '" data-name="' + G[D].name + '" data-id="m' + G[D].name + '">' + G[D].num + '</span><span class="l cashNum" data-type="+">+</span></div></div></div></div></li>';
                            if (F.length > 0) {
                                F.append(E)
                            }
                        } else {
                            if ($("#m" + G[D].name).length > 0) {
                                C = $("#m" + G[D].name);
                                C.find("img").attr("src", G[D].img);
                                C.find("h2").text(G[D].dishName);
                                C.find(".addNum").hide();
                                C.find(".addNum").siblings(".sex_box").show();
                                C.find(".addNum").siblings(".sex_box").find(".cashNumShow").text(G[D].num)
                            }
                        }
                    }
                }
                p(s, "+", localStorage.getItem("price"));
                d(i, localStorage.getItem("num"))
            }
        };
        v();
        x();
        z.bind("click", function () {
            var E = $(this), D = E.data("true"), B = {}, C = "";
            if (D === "true") {
                y = localStorage.getItem("resid");
                u = localStorage.getItem("num");
                w = localStorage.getItem("price");
                A = localStorage.getItem("dish");
                B = {resid: y, num: u, price: w, dish: A};
                if (z.hasClass("orderDish")) {
                    if ($.allpass()) {
                        B = t("#bookFillForm", B);
                        $.ajaxFn(this, E.data("href"), B, function (F) {
                            if (F.succ && z.hasClass("orderDish")) {
                                localStorage.removeItem("resid");
                                localStorage.removeItem("num");
                                localStorage.removeItem("price");
                                localStorage.removeItem("dish")
                            }
                        })
                    }
                } else {
                    $.ajaxFn(this, E.data("href"), B)
                }
            }
            return false
        })
    };
    a()
};
var xmsCashMibi = function () {
    var i = $("#cashNumForm"), j = $("#cashTel"), l = $("#cashNumber"), h = $("#cashShouldPay"), m = 0, k = parseInt($("#bmBalance").html(), 10) || 0, d = l.attr("data-price"), a = $("#cashMibi"), c, g = $("#rmbBalance"), o = $("#reduceRmb"), n = $("#remainRmb"), f = parseFloat(g.html(), 10) || 0, e = $("#cashMibiShow");
    var b = function () {
        c = parseInt(l.val(), 10);
        var p = l.attr("max");
        if (!c || c <= 0) {
            c = 1;
            l.val(c)
        }
        if (p && c > p) {
            c = p;
            l.val(c)
        }
        a.val("0");
        $("#cashMibiShow").text("0");
        if (c * d > f) {
            m = c * d - f;
            o.text(f);
            n.val(f)
        } else {
            m = 0;
            o.text(c * d);
            n.val(c * d)
        }
        if (k > 0) {
            if (m * 100 < k) {
                a.attr("max", m * 100)
            } else {
                a.attr("max", k)
            }
        }
        $("#cashAccPay").text(m.toFixed(2));
        $("#sumTotal").text(c)
    };
    b();
    $("#cashNumDesc").bind("click", function () {
        l.val(l.val() - 1);
        b();
        return false
    });
    $("#cashNumAsc").bind("click", function () {
        l.val(Number(l.val()) + 1);
        b();
        return false
    });
    l.bind("input",function () {
        if (this.value !== "") {
            b()
        }
    }).bind("blur", function () {
        if (this.value === "") {
            b()
        }
    });
    i.bind("submit", function () {
        if (!$.allpass()) {
            return false
        } else {
        }
    });
    j.bind("input", function () {
        var p = $(this).val();
        $("#cashEditTel").text(p + "，")
    });
    a.bind("input",function () {
        var t = parseInt(this.value, 10) || 0, s = $(this).attr("min"), q = $(this).attr("max"), r = Number($(this).attr("data-rate"));
        if (t >= s && t <= q) {
            var p = Math.round((m - t / r) * 100) / 100;
            $("#cashAccPay").text(p.toFixed(2));
            $("#cashAccPayInp").val(p.toFixed(2));
            $("#remainMibi").val(t);
            if (this.value !== "") {
                this.value = t
            }
            if (t == 0) {
                e.text("");
                this.value = ""
            } else {
                e.text(t)
            }
        } else {
            if (t > q) {
                this.value = q;
                e.text(q)
            } else {
                this.value = s;
                e.text(s)
            }
            $(this).trigger("input")
        }
    }).bind("blur",function () {
        if (this.value === "") {
            this.value = "0";
            e.text("0")
        }
    }).bind("focus", function () {
        if (this.value == "0") {
            this.value = "";
            e.text("")
        }
    })
};
var xmsLoginResit = function () {
    var d = $("#loginForm"), c = $("#registForm");
    eleLoginName = $("#loginName");
    var b = "xmsAccount", a = window.localStorage && localStorage.getItem(b);
    if (a) {
        eleLoginName.val(a)
    }
    d.bind("submit", function () {
        if ($.allpass($(".loginInput"))) {
            $.form(this)
        }
        return false
    });
    c.bind("submit", function () {
        var e = $("#registPwd");
        if ($.allpass($(".registInput"))) {
            if (e.length && (e.val().length < 6 || e.val().length > 16)) {
                alert("密码6-16位");
                e.get(0).focus()
            } else {
                $.form(this)
            }
        }
        return false
    });
    collectFn($("#noLogin"), $("#noLoginVal"), "ico_y", "ico_x")
};
$("#switchToPc").bind("click", function () {
    $.ajax({url: $(this).attr("data-url"), dataType: "json", success: function (a) {
        $.jsonHandle(a)
    }, error: function () {
        alert("由于网络或设备的原因，目前无法执行切换。")
    }})
});
var cancelOrder = function () {
    $("#cancelOrder").bind("submit", function () {
        if (confirm("确定要取消订单吗？")) {
            $.form(this)
        }
        return false
    })
};
var xmsBonus = function () {
    window.addEventListener("orientationchange", function () {
        window.location.reload()
    }, false)
};
var closeBox = function () {
    $("#close").bind("click", function () {
        $("#bonusBox").remove();
        $.overlay.hide()
    });
    $("#overlay").bind("click", function () {
        $("#bonusBox").remove();
        $.overlay.hide()
    });
    $("#overlay").bind("touchmove", function (a) {
        a.preventDefault()
    })
};
var xmsDetailSlide = function () {
    var h = $(".slide"), f = 0;
    var g = $("#detailSlide"), b = {};
    if (h.length <= 1) {
        return
    }
    var a = h.length * 320 + "px";
    g.css("width", a);
    var d = function () {
        if (f >= h.length) {
            f = 0
        } else {
            if (f < 0) {
                f = h.length - 1
            }
        }
        g.css("left", -320 * f + "px");
        c()
    };
    var e;
    var c = function () {
        e = setTimeout(function () {
            d();
            f += 1
        }, 3000)
    };
    c();
    g.bind("touchstart", function (i) {
        clearTimeout(e);
        b.x = i.touches[0].pageX
    });
    g.bind("touchmove", function (i) {
        b.x2 = i.touches[0].pageX;
        i.preventDefault()
    });
    g.bind("touchend", function () {
        clearTimeout(e);
        if (b.x2 - b.x <= -30) {
            f += 1;
            d()
        } else {
            if (b.x2 - b.x >= 30) {
                f -= 1;
                d()
            }
        }
    })
};
(function (a) {
    a.fn.nicewall = function (b) {
        window.addEventListener("orientationchange", function () {
            window.location.reload()
        }, false);
        a.fn.nicewall.defaults = {wrap: "body", width: 150, gap: 10, url: "", callback: null};
        var c = a.extend({}, a.fn.nicewall.defaults, b || {});
        return this.each(function () {
            var k = a(this), q = [], o = [], m = 0, l = true, g = c.width + c.gap, n = a("#loadImg");
            var f = Math.floor(a(c.wrap).offset().width / g);

            function e() {
                f = Math.floor(a(c.wrap).offset().width / g);
                for (var r = 0; r < f; r++) {
                    q[r] = g * r;
                    o[r] = 0
                }
            }

            e();
            function d() {
                if (!l) {
                    return
                }
                m++;
                l = false;
                a.getJSON(c.url, {page: m}, function (i) {
                    if (i.length >= 0) {
                        a.each(i, function (x, v) {
                            var r = a("<div class='nice_wall'><div class='g3 p5 b ell'>" + v.title + "</div></div>"), y = "<div class='ovh p5'><span class='l cr'>" + v.price + "</span><span class='r'><img src='http://s1.95171.cn/b/m/img/rq.png' class='rq'/>" + v.click + "</span></div>", w = a("<img>"), u = a("<a href=" + v.image + "></a>"), t = (v.height / v.width) * c.width, z = p();
                            r.css({position: "absolute", width: c.width + "px", left: q[z] + "px", top: o[z] + "px"});
                            if (v.textshow == 1) {
                                u.append(w);
                                r.append(y);
                                o[z] += t + 60 + c.gap;
                                r.prepend(u)
                            } else {
                                if (v.textshow == 0) {
                                    u.append(w);
                                    o[z] += t + 30 + c.gap;
                                    r.prepend(u)
                                } else {
                                    if (v.textshow == 2) {
                                        r.append(y);
                                        o[z] += 60 + c.gap
                                    }
                                }
                            }
                            k.prepend(r);
                            if (v.preview != "") {
                                var s = new Image();
                                s.src = v.preview;
                                s.onload = function () {
                                    w.attr({src: this.src, })
                                }
                            }
                            l = true
                        });
                        a(".footer") && a(".footer").hide()
                    }
                    if (m == 1 && i.length == 0) {
                        a(".footer") && a(".footer").show();
                        n.html("<div class='error'>您查找的数据不存在！</div>").show()
                    }
                    if (c.callback) {
                        c.callback()
                    }
                })
            }

            d();
            function p() {
                var r = o[0], t = 0;
                for (var s = 0; s < o.length; s++) {
                    if (o[s] < r) {
                        r = o[s];
                        t = s
                    }
                }
                return t
            }

            var h = true;
            a(window).off("scroll", j).on("scroll", j);
            function j() {
                if (h == true) {
                    var i = a.scrollTop() + a.clientHeight(), r = p();
                    if (Math.floor(o[r] - 1000) < i) {
                        d()
                    }
                    h = false;
                    setTimeout(function () {
                        h = true
                    }, 200)
                }
            }
        })
    }
})(jq);
var collectFn = function (g, f, d, a, c, h) {
    if (!g || !f || !d || !a) {
        return
    }
    var b;
    var e = function () {
        b = f.val();
        if (b == 0) {
            g.removeClass(d).addClass(a);
            b = f.val("1")
        } else {
            if (b == 1) {
                g.removeClass(a).addClass(d);
                b = f.val("0")
            }
        }
    };
    g.bind("click", function () {
        e();
        if (!!c) {
            var i = h || "由于网络的原因，您刚才的操作没有成功。";
            $.ajax({url: c, type: "POST", data: b, dataType: "json", success: function (j) {
                $.overlay.hide();
                $.jsonHandle(j);
                if (j.succ == false) {
                    e()
                }
            }, error: function () {
                e();
                $.overlay.hide();
                alert(i)
            }})
        }
    })
};
var changeMore = function () {
    var a = $("#Ellipsis"), b = $("#Full");
    $("#infoMore").bind("click", function () {
        var c = $(this);
        if (a.hasClass("dn")) {
            a.removeClass("dn");
            b.addClass("dn");
            c.html("展开<i class='triangle_down'></i>")
        } else {
            b.removeClass("dn");
            a.addClass("dn");
            c.html("收起<i class='triangle_up'></i>")
        }
    })
};