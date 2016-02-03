// Isotope 1.5.26.
;
(function(n, f, i) {
    var s = n.document;
    var w = s.documentElement;
    var k = n.Modernizr;
    var p = function(z) {
        return z.charAt(0).toUpperCase() + z.slice(1)
    };
    var u = "Moz Webkit O Ms".split(" ");
    var o = function(D) {
        var C = w.style,
            A;
        if (typeof C[D] === "string") {
            return D
        }
        D = p(D);
        for (var B = 0, z = u.length; B < z; B++) {
            A = u[B] + D;
            if (typeof C[A] === "string") {
                return A
            }
        }
    };
    var c = o("transform"),
        v = o("transitionProperty");;
    var j = {
        csstransforms: function() {
            return !!c
        },
        csstransforms3d: function() {
            var B = !! o("perspective");
            if (B && "webkitPerspective" in w.style) {
                var A = f("<style>@media (transform-3d),(-webkit-transform-3d){#modernizr{height:3px}}</style>").appendTo("head"),
                    z = f('<div id="modernizr" />').appendTo("html");
                B = z.height() === 3;
                z.remove();
                A.remove()
            }
            return B
        },
        csstransitions: function() {
            return !!v
        }
    };
    var m;
    if (k) {
        for (m in j) {
            if (!k.hasOwnProperty(m)) {
                k.addTest(m, j[m])
            }
        }
    } else {
        k = n.Modernizr = {
            _version: "1.6ish: miniModernizr for Isotope"
        };
        var y = " ";
        var q;
        for (m in j) {
            q = j[m]();
            k[m] = q;
            y += " " + (q ? "" : "no-") + m
        }
        f("html").addClass(y)
    } if (k.csstransforms) {
        var e = k.csstransforms3d ? {
            translate: function(z) {
                return "translate3d(" + z[0] + "px, " + z[1] + "px, 0) "
            },
            scale: function(z) {
                return "scale3d(" + z + ", " + z + ", 1) "
            }
        } : {
            translate: function(z) {
                return "translate(" + z[0] + "px, " + z[1] + "px) "
            },
            scale: function(z) {
                return "scale(" + z + ") "
            }
        };
        var t = function(C, A, H) {
            var F = f.data(C, "isoTransform") || {}, J = {}, B, D = {}, G;
            J[A] = H;
            f.extend(F, J);
            for (B in F) {
                G = F[B];
                D[B] = e[B](G)
            }
            var E = D.translate || "",
                I = D.scale || "",
                z = E + I;
            f.data(C, "isoTransform", F);
            C.style[c] = z
        };
        f.cssNumber.scale = true;
        f.cssHooks.scale = {
            set: function(z, A) {
                t(z, "scale", A)
            },
            get: function(B, A) {
                var z = f.data(B, "isoTransform");
                return z && z.scale ? z.scale : 1
            }
        };
        f.fx.step.scale = function(z) {
            f.cssHooks.scale.set(z.elem, z.now + z.unit)
        };
        f.cssNumber.translate = true;
        f.cssHooks.translate = {
            set: function(z, A) {
                t(z, "translate", A)
            },
            get: function(B, A) {
                var z = f.data(B, "isoTransform");
                return z && z.translate ? z.translate : [0, 0]
            }
        }
    }
    var b, a;
    if (k.csstransitions) {
        b = {
            WebkitTransitionProperty: "webkitTransitionEnd",
            MozTransitionProperty: "transitionend",
            OTransitionProperty: "oTransitionEnd otransitionend",
            transitionProperty: "transitionend"
        }[v];
        a = o("transitionDuration")
    }
    var l = f.event,
        h = f.event.handle ? "handle" : "dispatch",
        d;
    l.special.smartresize = {
        setup: function() {
            f(this).bind("resize", l.special.smartresize.handler)
        },
        teardown: function() {
            f(this).unbind("resize", l.special.smartresize.handler)
        },
        handler: function(C, z) {
            var B = this,
                A = arguments;
            C.type = "smartresize";
            if (d) {
                clearTimeout(d)
            }
            d = setTimeout(function() {
                l[h].apply(B, A)
            }, z === "execAsap" ? 0 : 100)
        }
    };
    f.fn.smartresize = function(z) {
        return z ? this.bind("smartresize", z) : this.trigger("smartresize", ["execAsap"])
    };
    f.Isotope = function(z, A, B) {
        this.element = f(A);
        this._create(z);
        this._init(B)
    };
    var g = ["width", "height"];
    var r = f(n);
    f.Isotope.settings = {
        resizable: true,
        layoutMode: "masonry",
        containerClass: "isotope",
        itemClass: "isotope-item",
        hiddenClass: "isotope-hidden",
        hiddenStyle: {
            opacity: 0,
            scale: 0.001
        },
        visibleStyle: {
            opacity: 1,
            scale: 1
        },
        containerStyle: {
            position: "relative",
            overflow: "hidden"
        },
        animationEngine: "best-available",
        animationOptions: {
            queue: false,
            duration: 800
        },
        sortBy: "original-order",
        sortAscending: true,
        resizesContainer: true,
        transformsEnabled: true,
        itemPositionDataEnabled: false
    };
    f.Isotope.prototype = {
        _create: function(E) {
            this.options = f.extend({}, f.Isotope.settings, E);
            this.styleQueue = [];
            this.elemCount = 0;
            var C = this.element[0].style;
            this.originalStyle = {};
            var B = g.slice(0);
            for (var G in this.options.containerStyle) {
                B.push(G)
            }
            for (var F = 0, A = B.length; F < A; F++) {
                G = B[F];
                this.originalStyle[G] = C[G] || ""
            }
            this.element.css(this.options.containerStyle);
            this._updateAnimationEngine();
            this._updateUsingTransforms();
            var D = {
                "original-order": function(I, H) {
                    H.elemCount++;
                    return H.elemCount
                },
                random: function() {
                    return Math.random()
                }
            };
            this.options.getSortData = f.extend(this.options.getSortData, D);
            this.reloadItems();
            this.offset = {
                left: parseInt((this.element.css("padding-left") || 0), 10),
                top: parseInt((this.element.css("padding-top") || 0), 10)
            };
            var z = this;
            setTimeout(function() {
                z.element.addClass(z.options.containerClass)
            }, 0);
            if (this.options.resizable) {
                r.bind("smartresize.isotope", function() {
                    z.resize()
                })
            }
            this.element.delegate("." + this.options.hiddenClass, "click", function() {
                return false
            })
        },
        _getAtoms: function(C) {
            var z = this.options.itemSelector,
                B = z ? C.filter(z).add(C.find(z)) : C,
                A = {
                    position: "absolute"
                };
            B = B.filter(function(D, E) {
                return E.nodeType === 1
            });
            if (this.usingTransforms) {
                A.left = 0;
                A.top = 0
            }
            B.css(A).addClass(this.options.itemClass);
            this.updateSortData(B, true);
            return B
        },
        _init: function(z) {
            this.$filteredAtoms = this._filter(this.$allAtoms);
            this._sort();
            this.reLayout(z)
        },
        option: function(B) {
            if (f.isPlainObject(B)) {
                this.options = f.extend(true, this.options, B);
                var z;
                for (var A in B) {
                    z = "_update" + p(A);
                    if (this[z]) {
                        this[z]()
                    }
                }
            }
        },
        _updateAnimationEngine: function() {
            var A = this.options.animationEngine.toLowerCase().replace(/[ _\-]/g, "");
            var z;
            switch (A) {
                case "css":
                case "none":
                    z = false;
                    break;
                case "jquery":
                    z = true;
                    break;
                default:
                    z = !k.csstransitions
            }
            this.isUsingJQueryAnimation = z;
            this._updateUsingTransforms()
        },
        _updateTransformsEnabled: function() {
            this._updateUsingTransforms()
        },
        _updateUsingTransforms: function() {
            var z = this.usingTransforms = this.options.transformsEnabled && k.csstransforms && k.csstransitions && !this.isUsingJQueryAnimation;
            if (!z) {
                delete this.options.hiddenStyle.scale;
                delete this.options.visibleStyle.scale
            }
            this.getPositionStyles = z ? this._translate : this._positionAbs
        },
        _filter: function(F) {
            var B = this.options.filter === "" ? "*" : this.options.filter;
            if (!B) {
                return F
            }
            var E = this.options.hiddenClass,
                A = "." + E,
                D = F.filter(A),
                z = D;
            if (B !== "*") {
                z = D.filter(B);
                var C = F.not(A).not(B).addClass(E);
                this.styleQueue.push({
                    $el: C,
                    style: this.options.hiddenStyle
                })
            }
            this.styleQueue.push({
                $el: z,
                style: this.options.visibleStyle
            });
            z.removeClass(E);
            return F.filter(B)
        },
        updateSortData: function(E, B) {
            var A = this,
                C = this.options.getSortData,
                D, z;
            E.each(function() {
                D = f(this);
                z = {};
                for (var F in C) {
                    if (!B && F === "original-order") {
                        z[F] = f.data(this, "isotope-sort-data")[F]
                    } else {
                        z[F] = C[F](D, A)
                    }
                }
                f.data(this, "isotope-sort-data", z)
            })
        },
        _sort: function() {
            var C = this.options.sortBy,
                B = this._getSorter,
                z = this.options.sortAscending ? 1 : -1,
                A = function(G, F) {
                    var E = B(G, C),
                        D = B(F, C);
                    if (E === D && C !== "original-order") {
                        E = B(G, "original-order");
                        D = B(F, "original-order")
                    }
                    return ((E > D) ? 1 : (E < D) ? -1 : 0) * z
                };
            this.$filteredAtoms.sort(A)
        },
        _getSorter: function(z, A) {
            return f.data(z, "isotope-sort-data")[A]
        },
        _translate: function(z, A) {
            return {
                translate: [z, A]
            }
        },
        _positionAbs: function(z, A) {
            return {
                left: z,
                top: A
            }
        },
        _pushPosition: function(B, A, C) {
            A = Math.round(A + this.offset.left);
            C = Math.round(C + this.offset.top);
            var z = this.getPositionStyles(A, C);
            this.styleQueue.push({
                $el: B,
                style: z
            });
            if (this.options.itemPositionDataEnabled) {
                B.data("isotope-item-position", {
                    x: A,
                    y: C
                })
            }
        },
        layout: function(C, B) {
            var A = this.options.layoutMode;
            this["_" + A + "Layout"](C);
            if (this.options.resizesContainer) {
                var z = this["_" + A + "GetContainerSize"]();
                this.styleQueue.push({
                    $el: this.element,
                    style: z
                })
            }
            this._processStyleQueue(C, B);
            this.isLaidOut = true
        },
        _processStyleQueue: function(A, P) {
            var C = !this.isLaidOut ? "css" : (this.isUsingJQueryAnimation ? "animate" : "css"),
                F = this.options.animationOptions,
                G = this.options.onLayout,
                N, D, J, K;
            D = function(Q, R) {
                R.$el[C](R.style, F)
            };
            if (this._isInserting && this.isUsingJQueryAnimation) {
                D = function(Q, R) {
                    N = R.$el.hasClass("no-transition") ? "css" : C;
                    R.$el[N](R.style, F)
                }
            } else {
                if (P || G || F.complete) {
                    var B = false,
                        I = [P, G, F.complete],
                        O = this;
                    J = true;
                    K = function() {
                        if (B) {
                            return
                        }
                        var S;
                        for (var R = 0, Q = I.length; R < Q; R++) {
                            S = I[R];
                            if (typeof S === "function") {
                                S.call(O.element, A, O)
                            }
                        }
                        B = true
                    };
                    if (this.isUsingJQueryAnimation && C === "animate") {
                        F.complete = K;
                        J = false
                    } else {
                        if (k.csstransitions) {
                            var H = 0,
                                L = this.styleQueue[0],
                                M = L && L.$el,
                                z;
                            while (!M || !M.length) {
                                z = this.styleQueue[H++];
                                if (!z) {
                                    return
                                }
                                M = z.$el
                            }
                            var E = parseFloat(getComputedStyle(M[0])[a]);
                            if (E > 0) {
                                D = function(Q, R) {
                                    R.$el[C](R.style, F).one(b, K)
                                };
                                J = false
                            }
                        }
                    }
                }
            }
            f.each(this.styleQueue, D);
            if (J) {
                K()
            }
            this.styleQueue = []
        },
        resize: function() {
            if (this["_" + this.options.layoutMode + "ResizeChanged"]()) {
                this.reLayout()
            }
        },
        reLayout: function(z) {
            this["_" + this.options.layoutMode + "Reset"]();
            this.layout(this.$filteredAtoms, z)
        },
        addItems: function(A, B) {
            var z = this._getAtoms(A);
            this.$allAtoms = this.$allAtoms.add(z);
            if (B) {
                B(z)
            }
        },
        insert: function(A, B) {
            this.element.append(A);
            var z = this;
            this.addItems(A, function(C) {
                var D = z._filter(C);
                z._addHideAppended(D);
                z._sort();
                z.reLayout();
                z._revealAppended(D, B)
            })
        },
        appended: function(A, B) {
            var z = this;
            this.addItems(A, function(C) {
                z._addHideAppended(C);
                z.layout(C);
                z._revealAppended(C, B)
            })
        },
        _addHideAppended: function(z) {
            this.$filteredAtoms = this.$filteredAtoms.add(z);
            z.addClass("no-transition");
            this._isInserting = true;
            this.styleQueue.push({
                $el: z,
                style: this.options.hiddenStyle
            })
        },
        _revealAppended: function(A, B) {
            var z = this;
            setTimeout(function() {
                A.removeClass("no-transition");
                z.styleQueue.push({
                    $el: A,
                    style: z.options.visibleStyle
                });
                z._isInserting = false;
                z._processStyleQueue(A, B)
            }, 10)
        },
        reloadItems: function() {
            this.$allAtoms = this._getAtoms(this.element.children())
        },
        remove: function(B, C) {
            this.$allAtoms = this.$allAtoms.not(B);
            this.$filteredAtoms = this.$filteredAtoms.not(B);
            var z = this;
            var A = function() {
                B.remove();
                if (C) {
                    C.call(z.element)
                }
            };
            if (B.filter(":not(." + this.options.hiddenClass + ")").length) {
                this.styleQueue.push({
                    $el: B,
                    style: this.options.hiddenStyle
                });
                this._sort();
                this.reLayout(A)
            } else {
                A()
            }
        },
        shuffle: function(z) {
            this.updateSortData(this.$allAtoms);
            this.options.sortBy = "random";
            this._sort();
            this.reLayout(z)
        },
        destroy: function() {
            var B = this.usingTransforms;
            var A = this.options;
            this.$allAtoms.removeClass(A.hiddenClass + " " + A.itemClass).each(function() {
                var D = this.style;
                D.position = "";
                D.top = "";
                D.left = "";
                D.opacity = "";
                if (B) {
                    D[c] = ""
                }
            });
            var z = this.element[0].style;
            for (var C in this.originalStyle) {
                z[C] = this.originalStyle[C]
            }
            this.element.unbind(".isotope").undelegate("." + A.hiddenClass, "click").removeClass(A.containerClass).removeData("isotope");
            r.unbind(".isotope")
        },
        _getSegments: function(F) {
            var C = this.options.layoutMode,
                B = F ? "rowHeight" : "columnWidth",
                A = F ? "height" : "width",
                E = F ? "rows" : "cols",
                G = this.element[A](),
                z, D = this.options[C] && this.options[C][B] || this.$filteredAtoms["outer" + p(A)](true) || G;
            z = Math.floor(G / D);
            z = Math.max(z, 1);
            this[C][E] = z;
            this[C][B] = D
        },
        _checkIfSegmentsChanged: function(C) {
            var A = this.options.layoutMode,
                B = C ? "rows" : "cols",
                z = this[A][B];
            this._getSegments(C);
            return (this[A][B] !== z)
        },
        _masonryReset: function() {
            this.masonry = {};
            this._getSegments();
            var z = this.masonry.cols;
            this.masonry.colYs = [];
            while (z--) {
                this.masonry.colYs.push(0)
            }
        },
        _masonryLayout: function(B) {
            var z = this,
                A = z.masonry;
            B.each(function() {
                var G = f(this),
                    E = Math.ceil(G.outerWidth(true) / A.columnWidth);
                E = Math.min(E, A.cols);
                if (E === 1) {
                    z._masonryPlaceBrick(G, A.colYs)
                } else {
                    var H = A.cols + 1 - E,
                        D = [],
                        F, C;
                    for (C = 0; C < H; C++) {
                        F = A.colYs.slice(C, C + E);
                        D[C] = Math.max.apply(Math, F)
                    }
                    z._masonryPlaceBrick(G, D)
                }
            })
        },
        _masonryPlaceBrick: function(C, G) {
            var z = Math.min.apply(Math, G),
                I = 0;
            for (var B = 0, D = G.length; B < D; B++) {
                if (G[B] === z) {
                    I = B;
                    break
                }
            }
            var H = this.masonry.columnWidth * I,
                F = z;
            this._pushPosition(C, H, F);
            var E = z + C.outerHeight(true),
                A = this.masonry.cols + 1 - D;
            for (B = 0; B < A; B++) {
                this.masonry.colYs[I + B] = E
            }
        },
        _masonryGetContainerSize: function() {
            var z = Math.max.apply(Math, this.masonry.colYs);
            return {
                height: z
            }
        },
        _masonryResizeChanged: function() {
            return this._checkIfSegmentsChanged()
        },
        _fitRowsReset: function() {
            this.fitRows = {
                x: 0,
                y: 0,
                height: 0
            }
        },
        _fitRowsLayout: function(C) {
            var z = this,
                B = this.element.width(),
                A = this.fitRows;
            C.each(function() {
                var F = f(this),
                    E = F.outerWidth(true),
                    D = F.outerHeight(true);
                if (A.x !== 0 && E + A.x > B) {
                    A.x = 0;
                    A.y = A.height
                }
                z._pushPosition(F, A.x, A.y);
                A.height = Math.max(A.y + D, A.height);
                A.x += E
            })
        },
        _fitRowsGetContainerSize: function() {
            return {
                height: this.fitRows.height
            }
        },
        _fitRowsResizeChanged: function() {
            return true
        },
        _cellsByRowReset: function() {
            this.cellsByRow = {
                index: 0
            };
            this._getSegments();
            this._getSegments(true)
        },
        _cellsByRowLayout: function(B) {
            var z = this,
                A = this.cellsByRow;
            B.each(function() {
                var E = f(this),
                    D = A.index % A.cols,
                    F = Math.floor(A.index / A.cols),
                    C = (D + 0.5) * A.columnWidth - E.outerWidth(true) / 2,
                    G = (F + 0.5) * A.rowHeight - E.outerHeight(true) / 2;
                z._pushPosition(E, C, G);
                A.index++
            })
        },
        _cellsByRowGetContainerSize: function() {
            return {
                height: Math.ceil(this.$filteredAtoms.length / this.cellsByRow.cols) * this.cellsByRow.rowHeight + this.offset.top
            }
        },
        _cellsByRowResizeChanged: function() {
            return this._checkIfSegmentsChanged()
        },
        _straightDownReset: function() {
            this.straightDown = {
                y: 0
            }
        },
        _straightDownLayout: function(A) {
            var z = this;
            A.each(function(B) {
                var C = f(this);
                z._pushPosition(C, 0, z.straightDown.y);
                z.straightDown.y += C.outerHeight(true)
            })
        },
        _straightDownGetContainerSize: function() {
            return {
                height: this.straightDown.y
            }
        },
        _straightDownResizeChanged: function() {
            return true
        },
        _masonryHorizontalReset: function() {
            this.masonryHorizontal = {};
            this._getSegments(true);
            var z = this.masonryHorizontal.rows;
            this.masonryHorizontal.rowXs = [];
            while (z--) {
                this.masonryHorizontal.rowXs.push(0)
            }
        },
        _masonryHorizontalLayout: function(B) {
            var z = this,
                A = z.masonryHorizontal;
            B.each(function() {
                var G = f(this),
                    E = Math.ceil(G.outerHeight(true) / A.rowHeight);
                E = Math.min(E, A.rows);
                if (E === 1) {
                    z._masonryHorizontalPlaceBrick(G, A.rowXs)
                } else {
                    var H = A.rows + 1 - E,
                        D = [],
                        F, C;
                    for (C = 0; C < H; C++) {
                        F = A.rowXs.slice(C, C + E);
                        D[C] = Math.max.apply(Math, F)
                    }
                    z._masonryHorizontalPlaceBrick(G, D)
                }
            })
        },
        _masonryHorizontalPlaceBrick: function(C, H) {
            var z = Math.min.apply(Math, H),
                F = 0;
            for (var B = 0, D = H.length; B < D; B++) {
                if (H[B] === z) {
                    F = B;
                    break
                }
            }
            var I = z,
                G = this.masonryHorizontal.rowHeight * F;
            this._pushPosition(C, I, G);
            var E = z + C.outerWidth(true),
                A = this.masonryHorizontal.rows + 1 - D;
            for (B = 0; B < A; B++) {
                this.masonryHorizontal.rowXs[F + B] = E
            }
        },
        _masonryHorizontalGetContainerSize: function() {
            var z = Math.max.apply(Math, this.masonryHorizontal.rowXs);
            return {
                width: z
            }
        },
        _masonryHorizontalResizeChanged: function() {
            return this._checkIfSegmentsChanged(true)
        },
        _fitColumnsReset: function() {
            this.fitColumns = {
                x: 0,
                y: 0,
                width: 0
            }
        },
        _fitColumnsLayout: function(C) {
            var z = this,
                B = this.element.height(),
                A = this.fitColumns;
            C.each(function() {
                var F = f(this),
                    E = F.outerWidth(true),
                    D = F.outerHeight(true);
                if (A.y !== 0 && D + A.y > B) {
                    A.x = A.width;
                    A.y = 0
                }
                z._pushPosition(F, A.x, A.y);
                A.width = Math.max(A.x + E, A.width);
                A.y += D
            })
        },
        _fitColumnsGetContainerSize: function() {
            return {
                width: this.fitColumns.width
            }
        },
        _fitColumnsResizeChanged: function() {
            return true
        },
        _cellsByColumnReset: function() {
            this.cellsByColumn = {
                index: 0
            };
            this._getSegments();
            this._getSegments(true)
        },
        _cellsByColumnLayout: function(B) {
            var z = this,
                A = this.cellsByColumn;
            B.each(function() {
                var E = f(this),
                    D = Math.floor(A.index / A.rows),
                    F = A.index % A.rows,
                    C = (D + 0.5) * A.columnWidth - E.outerWidth(true) / 2,
                    G = (F + 0.5) * A.rowHeight - E.outerHeight(true) / 2;
                z._pushPosition(E, C, G);
                A.index++
            })
        },
        _cellsByColumnGetContainerSize: function() {
            return {
                width: Math.ceil(this.$filteredAtoms.length / this.cellsByColumn.rows) * this.cellsByColumn.columnWidth
            }
        },
        _cellsByColumnResizeChanged: function() {
            return this._checkIfSegmentsChanged(true)
        },
        _straightAcrossReset: function() {
            this.straightAcross = {
                x: 0
            }
        },
        _straightAcrossLayout: function(A) {
            var z = this;
            A.each(function(B) {
                var C = f(this);
                z._pushPosition(C, z.straightAcross.x, 0);
                z.straightAcross.x += C.outerWidth(true)
            })
        },
        _straightAcrossGetContainerSize: function() {
            return {
                width: this.straightAcross.x
            }
        },
        _straightAcrossResizeChanged: function() {
            return true
        }
    };;
    f.fn.imagesLoaded = function(G) {
        var E = this,
            C = E.find("img").add(E.filter("img")),
            z = C.length,
            F = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==",
            B = [];

        function D() {
            G.call(E, C)
        }

        function A(I) {
            var H = I.target;
            if (H.src !== F && f.inArray(H, B) === -1) {
                B.push(H);
                if (--z <= 0) {
                    setTimeout(D);
                    C.unbind(".imagesLoaded", A)
                }
            }
        }
        if (!z) {
            D()
        }
        C.bind("load.imagesLoaded error.imagesLoaded", A).each(function() {
            var H = this.src;
            this.src = F;
            this.src = H
        });
        return E
    };
    var x = function(z) {
        if (n.console) {
            n.console.error(z)
        }
    };
    f.fn.isotope = function(A, B) {
        if (typeof A === "string") {
            var z = Array.prototype.slice.call(arguments, 1);
            this.each(function() {
                var C = f.data(this, "isotope");
                if (!C) {
                    x("cannot call methods on isotope prior to initialization; attempted to call method '" + A + "'");
                    return
                }
                if (!f.isFunction(C[A]) || A.charAt(0) === "_") {
                    x("no such method '" + A + "' for isotope instance");
                    return
                }
                C[A].apply(C, z)
            })
        } else {
            this.each(function() {
                var C = f.data(this, "isotope");
                if (C) {
                    C.option(A);
                    C._init(B)
                } else {
                    f.data(this, "isotope", new f.Isotope(A, this, B))
                }
            })
        }
        return this
    }
})(window, jQuery);
// Isoptope custom extensions and methods.
jQuery.Isotope.prototype._getMasonryGutterColumns = function() {
    var a = this.options.masonry && this.options.masonry.gutterWidth || 0;
    containerWidth = this.element.width();
    this.masonry.columnWidth = this.options.masonry && this.options.masonry.columnWidth || this.$filteredAtoms.outerWidth(true) || containerWidth;
    this.masonry.columnWidth += a;
    this.masonry.cols = Math.floor((containerWidth + a) / this.masonry.columnWidth);
    this.masonry.cols = Math.max(this.masonry.cols, 1)
};
jQuery.Isotope.prototype._masonryReset = function() {
    this.masonry = {};
    this._getMasonryGutterColumns();
    var a = this.masonry.cols;
    this.masonry.colYs = [];
    while (a--) {
        this.masonry.colYs.push(0)
    }
};
jQuery.Isotope.prototype._masonryResizeChanged = function() {
    var a = this.masonry.cols;
    this._getMasonryGutterColumns();
    return (this.masonry.cols !== a)
};