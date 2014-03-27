jQuery.fn.extend({
    statgrid: function(settings) {
        jQuery(this).each(function() {
            var grid = jQuery(this);
            var set = jQuery.extend({
                editable:           true, 
                calcOff:            false,
                log:                false,
                default_type:       'int',
                parent:             grid
            }, settings);
            grid.statgridInstance = jQuery.statgrid.getGrid(set, grid);
            jQuery.statgrid.instance = grid.statgridInstance;
            //parent.attr('statgridInstance', 1);
        });
        return this;
    }
});

jQuery.statgrid = {
    getGrid: function(s, g) {
        var jG = {
            cl: {
                cellActive:             'gridCellActive',
                cellHighlighted:        'gridCellHighighted',
                cellProtected:          'cellProtected',
                cellCalculated:         'cellCalculated',
                inPlaceEdit:            'gridInPlaceEdit',
                fullScreen:             'gridFullScreen',
                uiCellActive:           'ui-state-active',
                uiCellHighlighted:      'ui-state-highlight',
                uiInPlaceEdit:          'ui-state-active',
                uiFullScreen:           'ui-widget-content ui-corner-all',
                pane:                   'grid',
            },
            id: {
                inPlaceEdit:            'gridInPlaceEdit',
                inPlaceList:            'gridInPlaceList'
            },
            obj: {
                cellActive:         function() { return jQuery(jG.cellLast.td); },
                //cellHighlighted:    function() { return jQuery(jG.highlightedLast.td); },
                cellHighlighted:    function() { return jQuery('td.' + jG.cl.cellHighlighted); },
                cellProtected:      function() { return jQuery('td.' + jG.cl.cellProtected); },
                cellCalculated:     function() { return jQuery('td.' + jG.cl.cellCalculated); },
                formula:            function() { return jQuery('#' + jG.id.formula); },
                fullScreen:         function() { return jQuery('div.' + jG.cl.fullScreen); },
                inPlaceEdit:        function() { return jQuery('#gridInPlaceEdit'); },
                inPlaceList:        function() { return jQuery('#gridInPlaceList'); },
                pane:               function() { return jQuery('#grid'); },

            },
            nav: false,
            setNav: function(nav) {
                jG.nav = nav;
            },
            colLast: 0,
            rowLast: 0,
            isTd: function(o) { 
                o = (o[0] ? o[0] : [o]);
                if (o[0]) {
                    if (!isNaN(o[0].cellIndex)) { 
                        return true;
                    }
                }
                return false;
            },
            cellLast: {
                td: jQuery('<td />'), 
                row: -1,
                col: -1,
                isEdit: false
            }, 
            highlightedLast: {
                td: jQuery('<td />'),
                rowStart: -1,
                colStart: -1,
                rowEnd: -1,
                colEnd: -1
            },
            getTdId: function(row, col) { 
                return 'c' + col + '_r' + row;
            },
            getTd: function(row, col) { 
                return document.getElementById(jG.getTdId(row, col));
            },
            getTdLocation: function(td) { 
                if (!td || !td[0]) return {col: 0, row: 0};
                //td = (td[0] ? td[0] : [td]);
                return {
                    col: parseInt(td[0].cellIndex),
                    row: parseInt(td[0].parentNode.rowIndex)
                }
            },
            getTdLocationFromStr: function(locStr) { 
                re = /^c(\d+)_r(\d+)$/;
                res = re.exec(locStr);
                return {
                    row: parseInt(res[2]),  
                    col: parseInt(res[1])
                };
            },
            cell: {
                setActive: function() {
                    jG.cell.clearActive();
                    jG.cell.setHighlighted(
                        jG.cellLast.td.addClass(jG.cl.cellActive)
                    );
                },
                setHighlighted: function(td) {
                    jQuery(td)
                        .addClass(jG.cl.cellHighlighted + ' ' + jG.cl.uiCellHighlighted);
                },
                clearActive: function() {
                    jG.obj.cellActive()
                        .removeClass(jG.cl.cellActive);
                },
                isHighlighted: function() {
                    return (jG.highlightedLast.td ? true : false);
                },
                isProtected: function(td) {
                    return (td.hasClass(jG.cl.cellProtected) ? true : false);
                },
                isCalculated: function(td) {
                    return (td.hasClass(jG.cl.cellCalculated) ? true : false);
                },
                clearHighlighted: function() {
                    if (jG.cell.isHighlighted()) {
                        jG.obj.cellHighlighted()
                            .removeClass(jG.cl.cellHighlighted + ' ' + jG.cl.uiCellHighlighted);
                    }
                    
                    jG.highlightedLast.rowStart = -1;
                    jG.highlightedLast.colStart = -1;
                    jG.highlightedLast.rowEnd = -1;

                    jG.highlightedLast.colEnd = -1;
                    jG.highlightedLast.td = jQuery('<td />');
                }
            },
            cellEdit: function(td) { 
                jG.evt.cellEditDone();
                jG.followMe(td);
                jG.obj.pane().scroll();
                var loc = jG.getTdLocation(td);
                jG.cellSetActive(td, loc);
            },
            cellSetActive: function(td, loc) {
                if (typeof(loc.col) != 'undefined') {
                    jG.cellLast.td = td; 
                    jG.cellLast.row = jG.rowLast = loc.row;
                    jG.cellLast.col = jG.colLast = loc.col;
                    jG.cell.clearHighlighted();
                    jG.highlightedLast.td = td;
                    jG.cell.setActive(); 
                    jG.highlightedLast.rowStart = loc.row;
                    jG.highlightedLast.colStart = loc.col;
                    jG.highlightedLast.rowLast = loc.row;
                    jG.highlightedLast.colLast = loc.col;
                }
            },
            sheetSize: function(o) {
                var loc = jG.getTdLocation((o ? o : jG.obj.pane()).find('td:last'));
                return {
                    width: loc.col,
                    height: loc.row
                };
            },
            inPlaceEdit: function(td) { 
                jG.obj.inPlaceEdit().remove();
                var offset = td.offset();
                var style = td.attr('style');
                var type = td.attr('type');
                var w = td.width() + 2;
                var h = td.height() + 2;
                if (type == undefined) {
                    type = s.default_type;
                }
                if (type =='int' || type =='float') {
                    var textarea = jQuery('<textarea id="' + jG.id.inPlaceEdit + '" class="' + jG.cl.inPlaceEdit + ' ' + jG.cl.uiInPlaceEdit + '" />')
                        .css('left', offset.left)
                        .css('top', offset.top - 1)
                        .width(w)
                        .height(h)
                        .keydown(jG.evt.inPlaceEditOnKeyDown)
                        .focus(function() {
                            jG.setNav(false);
                        })
                        .focusout(function() {
                            jG.setNav(true);
                        })
                        .blur(function() {
                            jG.setNav(true);
                        })
                        .val(td.html())
                        .appendTo('body')
                        .focus()
                        .select();
                    if (type == 'int' ) {
                        textarea.numeric({ decimal : false });
                    } else if (type == 'float') {
                        textarea.numeric(); 
                    }
                } else {
                    var list = jQuery('#' + type).find('select').clone();
                    v = td.text();
                    td.text('');
                    list.attr('id', jG.id.inPlaceList)
                        .change(jG.evt.cellEditDone)
                        .focus(function() {
                            jG.setNav(false);
                        })
                        .focusout(function() {
                            jG.setNav(true);
                        })
                        .blur(function() {
                            jG.setNav(true);
                        })
                        .val(v)
                        .focus()
                        .select()
                        .appendTo(td).focus();
                    
                }
            },
            updateCell: function(val) { 
                var loc = {row: jG.cellLast.row,col: jG.cellLast.col};
                if (loc.row == -1 && loc.col == -1) return false; 
                var td = jQuery(jG.getTd(loc.row, loc.col));
                td.html(val)
                jG.setDirty(true);
                jG.evt.cellEditDone(true);
            },
            toggleFullScreen: function() { 
                if (jG.obj.fullScreen().is(':visible')) { 
                    jQuery('body').removeClass('bodyNoScroll');
                    jG.obj.pane().height(s.height).width(s.width);
                    jQuery('#adminForm').append(g);
                    jG.obj.fullScreen().remove();
                } else { 
                    jQuery('body').addClass('bodyNoScroll');
                    var w = $(window).width() - 15;
                    var h = $(window).height() - 35;
                    s.width = g.width();
                    s.height = g.height();
                    fs_pane = jQuery('<div class="' + jG.cl.fullScreen + ' ' + jG.cl.uiFullScreen + ' ui-widget" />')
                        .append(jQuery('<div id="fs_titlebar" class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">Выход из полноэкранного режима</div>'))
                        .append(g)
                        .appendTo('body');
                    jG.obj.pane().height(h).width(w);
                    jQuery('#fs_titlebar').click(function () {
                        jG.toggleFullScreen();
                    });
                }
            },
            followMe: function(td) { 
                td = (td ? td : jQuery(jG.cellLast.td));
                var pane = jG.obj.pane();
                var panePos = pane.offset();
                var paneWidth = pane.width();
                var paneHeight = pane.height();

                var tdPos = td.offset();
                var tdWidth = td.width();
                var tdHeight = td.height();
                
                var margin = 20;

                if ((tdPos.left + tdWidth + margin) > (panePos.left + paneWidth)) { //right
                    pane.stop().scrollTo(td, {
                        axis: 'x',
                        duration: 50,
                        offset: - ((paneWidth - tdWidth) - margin)
                    });
                } else if (tdPos.left < panePos.left) { //left
                    pane.stop().scrollTo(td, {
                        axis: 'x',
                        duration: 50
                    });
                }
                
                if ((tdPos.top + tdHeight + margin) > (panePos.top + paneHeight)) { //bottom
                    pane.stop().scrollTo(td, {
                        axis: 'y',
                        duration: 50,
                        offset: - ((paneHeight - tdHeight) - margin)
                    });
                } else if (tdPos.top < panePos.top) { //top
                    pane.stop().scrollTo(td, {
                        axis: 'y',
                        duration: 50
                    });
                }
            },
            cellCalculated: false, 
            updateCalcCell: function() {
                cc = jG.cellCalculated;
                if (!cc) {
                    cc = jG.obj.cellCalculated();
                }
                cc.each(function(i) {
                    var cell = jQuery(this);
                    var cells = cell.attr('cellRange');
                    var fn = cell.attr('formula');
                    cells = cells.split(';');
                    var values = [];
                    for (i = 0; i < cells.length; i++) {
                        values = values.concat(jG.cellIdHandlers.cellRangeValue(cells[i]));
                    }
                    v = jG.cellIdHandlers.callFunction(fn, values);
                    cell.html(v);
                });
            },
            cellIdHandlers: {
                cellValue: function(id) {
                    var loc = jG.cellIdHandlers.parseLocation(id);
                    td = jQuery(jG.getTd(loc.row, loc.col));
                    v = parseInt(td.text());
                    return v;
                },
                cellRangeValue: function(ids) {
                    var result = [];
                    ids = ids.split(':');
                    if (ids.length == 1) {
                        result.push(jG.cellIdHandlers.cellValue(ids[0]));
                    } 
                    else {
                        var start = jG.cellIdHandlers.parseLocation(ids[0]);
                        var end = jG.cellIdHandlers.parseLocation(ids[1]);
                        for (var i = start.row; i <= end.row; i++) {
                            for (var j = start.col; j <= end.col; j++) {
                                td = jQuery(jG.getTd(i, j));
                                v = parseInt(td.text());
                                if (isNaN(v)) {v = 0;}
                                result.push(v);
                            }
                        }
                    }
                    return result;
                },
                callFunction: function(fn, args) {
                    switch (fn) {
                        case "sum": 
                            for(var i=0, v=0;i<args.length; v+=args[i++]);
                            break;
                        case "subtract": 
                                if (args.length > 2 || args.length < 1) { return 0; }
                                if (args[1] > args[0]) { return 0; }
                                v = args[0]-args[1];
                                v = v.toFixed(2);
                            break;
                        case "%":
                                if (args.length > 2 || args.length < 1) { return 0; }
                                if (args[1] == 0) { return '#ДЕЛ/0!'; }
                                v = args[0]/args[1]*100;
                                v = v.toFixed(2);
                            break;
                    }
                    if (isNaN(v)) {v = '';}
                    if (!isFinite(v)) {v = '';}
                    return v;
                },
                parseLocation: function(locStr) { 
                    re = /^c(\d+)r(\d+)$/;
                    res = re.exec(locStr);
                    return {
                        row: parseInt(res[2]),  
                        col: parseInt(res[1])
                    };
                }
            },
            isDirty:  false,
            setDirty: function(dirty) { jG.isDirty = dirty; },
            evt: { 
                keyDownHandler: {
                    enterOnInPlaceEdit: function(e) {
                        if (!e.shiftKey) {
                            return jG.evt.cellSetFocusFromKeyCode(e);
                        } else {
                            return true;
                        }
                    },
                    tab: function(e) {
                        return jG.evt.cellSetFocusFromKeyCode(e);
                    },
                    pageUpDown: function(reverse) {
                        var pane = jG.obj.pane();
                        var left = jG.cellLast.td.position().left;
                        var top = 0;
                        
                        if (reverse) {
                            top = 0;
                            pane.scrollTop(pane.scrollTop() - pane.height());
                            
                        } else {
                            top = pane.height();
                            pane.scrollTop(pane.scrollTop() + top);
                        }
                    },
                    pasteOverCells: function(e) { 
                        if (e.ctrlKey) {
                            if (typeof(window.clipboardData.getData("Text")) == 'string') {
                                var val = window.clipboardData.getData("Text"); 
                                locStr = jG.obj.cellHighlighted().attr('id');
                                loc = jG.getTdLocationFromStr(locStr);
                                var row = val.split(/\n/g);
                                var newValCount = 0;
                                for (var i = 0; i < row.length; i++) {
                                    var col = row[i].split(/\t/g); 
                                    for (var j = 0; j < col.length; j++) {
                                        if (col[j]) {
                                            v = col[j].split(',').join('.');
                                            v = parseFloat(v);
                                            if (v > 0) {
                                                var td = jQuery(jG.getTd(i + loc.row, j + loc.col));
                                                if (!jG.cell.isProtected(td) && !jG.cell.isCalculated(td)) {
                                                    td.html(v);
                                                    newValCount++;
                                                }
                                            }
                                        }
                                    }
                                } 
                            } 
                        }
                        if (newValCount > 0) {
                            jG.setDirty(true);
                            jG.updateCalcCell();
                        }
                        return true;
                    },
                    documentKeydown: function(e) {
                        if (jG.nav) {
                            switch (e.keyCode) {
                                case key.TAB:       jG.evt.keyDownHandler.tab(e);
                                    break;
                                case key.ENTER:
                                case key.LEFT:
                                case key.UP:
                                case key.RIGHT:
                                case key.DOWN:
                                case key.HOME:
                                case key.END:       jG.evt.cellSetFocusFromKeyCode(e);
                                    break;
                                case key.PAGE_UP:   jG.evt.keyDownHandler.pageUpDown(true);
                                    break;
                                case key.PAGE_DOWN: jG.evt.keyDownHandler.pageUpDown();
                                    break;
                                case key.V:         jG.evt.keyDownHandler.pasteOverCells(e);
                                    break;
                                case key.ESCAPE:    jG.evt.cellEditAbandon();
                                    break;
                                case key.CONTROL: 
                                case key.CAPS_LOCK:
                                case key.SHIFT:
                                case key.ALT:
                                    break;
                                default:            jG.obj.cellActive().dblclick(); return true;
                            }
                            return false;
                        }
                    }
                },
                inPlaceEditOnKeyDown: function(e) {
                    switch (e.keyCode) {
                        case key.ENTER:     return jG.evt.keyDownHandler.enterOnInPlaceEdit(e);
                            break;
                        case key.TAB:       return jG.evt.keyDownHandler.tab(e);
                            break;
                        case key.ESCAPE:    jG.evt.cellEditAbandon(); return false;
                            break;
                    }
                },
                cellEditDone: function() { 
                    switch (jG.cellLast.isEdit) {
                        case true:
                            v = jG.obj.inPlaceEdit().val();
                            jG.obj.inPlaceEdit().remove();
                            if (!v) {
                                l = jG.obj.inPlaceList();
                                v = l.find('option:selected').text();
                                jG.obj.inPlaceList().remove();
                            }
                            var td = jG.cellLast.td;
                            var prevVal = td.text();
                            if (td && jG.cellLast.row > -1 && jG.cellLast.col > -1) {
                                td.html(v);
                                jG.cellLast.isEdit = false;
                                jG.setDirty(true);
                            }
                            if (v != prevVal) {
                                jG.updateCalcCell();
                            }
                            break;
                        default:
                    }
                    jG.setNav(true);
                },
                cellEditAbandon: function() { 
                    jG.obj.inPlaceEdit().remove();
                    jG.obj.inPlaceList().remove();
                    jG.cell.clearActive();
                    jG.cell.clearHighlighted();
                    jG.cellLast.td = jQuery('<td />');
                    jG.cellLast.row = -1;
                    jG.cellLast.col = -1;
                    jG.rowLast = -1;
                    jG.colLast = -1;
                    jG.setNav(true);
                    return false;
                },
                cellSetFocusFromKeyCode: function(e) { 
                    var c = jG.cellLast.col; 
                    var r = jG.cellLast.row;
                    var overrideIsEdit = false;
                    switch (e.keyCode) {
                        case key.UP:        r--; break;
                        case key.DOWN:      r++; break;
                        case key.LEFT:      c--; break;
                        case key.RIGHT:     c++; break;
                        case key.ENTER:     r++;
                            overrideIsEdit = true;
                            if (jG.highlightedLast.td.length > 1) {
                                var inPlaceEdit = jG.obj.inPlaceEdit();
                                var inPlaceList = jG.obj.inPlaceList();
                                var v = inPlaceEdit.val();
                                inPlaceEdit.remove();
                                inPlaceList.remove();
                                jG.updateCell(v);
                                return true;
                            } else if (jG.cellLast.row == jG.sheetSize().height) {
                                    r = jG.cellLast.row;
                            }
                            break;
                        case key.TAB:
                            overrideIsEdit = true;
                            if (e.shiftKey) {
                                c--;
                            } else {
                                c++;
                            }
                            break;
                        case key.HOME:      c = 0; break;
                        case key.END:       pane = jG.obj.pane();
                            c = pane.find('tr:first > td').length - 1;
                        break;
                    }
                    c = (c < 0 ? 0 : c);
                    r = (r < 0 ? 0 : r);
                    var td = jG.getTd(r, c);
                    if (!jG.cellLast.isEdit || overrideIsEdit) {
                        if (td) {
                            jG.cell.clearHighlighted();
                            td = jQuery(td);
                            if (td.is(':hidden')) {
                                function getNext(o, reverse) {
                                    if (reverse) {
                                        c++;
                                        o = o.next()
                                    }
                                    else {
                                        c--;
                                        o = o.prev();
                                    }
                                    if (o.is(':hidden') && o.length) {
                                        return getNext(o, reverse);
                                    }
                                    return o;
                                }
                                td = getNext(td, c > jG.cellLast.col);
                            }
                            jG.cellEdit(td);
                            return false;
                        }
                    }
                    return true;
                },
                cellOnMouseDown: function(e) {
                    jG.cellEdit(jQuery(e.target));
                },
                cellOnDblClick: function(e) {
                    td = jG.cellLast.td;
                    if (!jG.cell.isProtected(td) && !jG.cell.isCalculated(td)) {
                        jG.cellLast.isEdit = true;
                        jG.inPlaceEdit(td);
                    }
                }
            },
            exportJSON: function() {
                    var document = {}; //document
                    document['metadata'] = {};
                    document['data'] = {};
                    table = jG.obj.pane();
                    var trs = table.find('tr');
                    var rowCount = trs.length;
                    var colCount = 0;
                    trs.each(function(i) {
                        var tr = jQuery(this);
                        var tds = tr.find('td');
                        colCount = tds.length;
                        
                        document['data']['r' + i] = {};
                        document['data']['r' + i]['h'] = tr.attr('height');
                        
                        tds.each(function(j) {
                            var td = jQuery(this);
                            var colSpan = td.attr('colspan');
                            colSpan = (colSpan > 1 ? colSpan : null);
                            var formula = td.attr('formula');

                            document['data']['r' + i]['c' + j] = {
                                'value': (formula ? formula : td.text()),
                                'style': td.attr('style'),
                                'colspan': colSpan,
                                'cl': td.attr('class')
                            };
                        });
                    });
                    document['metadata'] = {
                        'columns': colCount, //length is 1 based, index is 0 based
                        'rows': rowCount, //length is 1 based, index is 0 based
                        'title': table.attr('title')
                    };
                    return document
            },
            exportHTML: function() {
                jG.evt.cellEditDone();
                g = jG.decorateRemove();
                s = jQuery('<table />').html(g).html();
                return s;
            },
            decorateRemove: function() {                                                       
                var o = jG.obj.pane().clone();
                jQuery(o).find('td.' + jG.cl.cellActive)
                    .removeClass(jG.cl.cellActive + ' ' + jG.cl.uiCellActive);
                jQuery(o).find('td.' + jG.cl.cellHighlighted)
                    .removeClass(jG.cl.cellHighlighted + ' ' + jG.cl.uiCellHighlighted);
                return o;
            }
        };
        jG.setNav(true);
        jQuery(document).keydown(jG.evt.keyDownHandler.documentKeydown);
        g.mousedown(function(e) {
                if (jG.isTd(e.target)) {
                    jG.evt.cellOnMouseDown(e);
                    return false;
                }
            })
        .dblclick(jG.evt.cellOnDblClick);
        return jG;
    }

};

var key = { 
    BACKSPACE:          8,
    CAPS_LOCK:          20,
    COMMA:              188,
    DOT:                190,
    CONTROL:            17,
    ALT:                18,
    DELETE:             46,
    DOWN:               40,
    END:                35,
    ENTER:              13,
    ESCAPE:             27,
    HOME:               36,
    INSERT:             45,
    LEFT:               37,
    NUMPAD_ADD:         107,
    NUMPAD_DECIMAL:     110,
    NUMPAD_DIVIDE:      111,
    NUMPAD_ENTER:       108,
    NUMPAD_MULTIPLY:    106,
    NUMPAD_SUBTRACT:    109,
    PAGE_DOWN:          34,
    PAGE_UP:            33,
    PERIOD:             190,
    RIGHT:              39,
    SHIFT:              16,
    SPACE:              32,
    TAB:                9,
    UP:                 38,
    F:                  70,
    V:                  86,
    Y:                  89,
    Z:                  90
};
