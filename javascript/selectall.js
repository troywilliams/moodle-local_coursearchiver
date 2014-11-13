M.local_coursearchiver = M.local_coursearchiver || {};
M.local_coursearchiver.selectall = {
    selectallcheckbox: 'selectall-checkbox',
    selectedcontainer: 'selected-container',
    init: function(Y, selectallcheckbox, selectedcontainer) {
        this.selectallcheckbox = selectallcheckbox;
        this.selectedcontainer = selectedcontainer;
        
        Y.use('node', function(Y) {
            checkboxes = Y.all('td.c0 input');
            checkboxes.each(function(node) {
                node.on('change', function(e) {
                    rowelement = e.currentTarget.get('parentNode').get('parentNode');
                    if (e.currentTarget.get('checked')) {
                        rowelement.setAttribute('class', 'selectedrow');
                    } else {
                        rowelement.setAttribute('class', 'unselectedrow');
                    }
                });

                rowelement = node.get('parentNode').get('parentNode');
                if (node.get('checked')) {
                    rowelement.setAttribute('class', 'selectedrow');
                } else {
                    rowelement.setAttribute('class', 'unselectedrow');
                }
            });
        });
        
        Y.one('input[name="sa-checkbox"]').on('click', Y.bind(this.toggle_all, this));
        Y.all('.sa-submit').on('click', Y.bind(this.submitted, this));
        
    },
    
    submitted: function(e) {
        var selecteditems = [];
        checkboxes = Y.all('td.c0 input');
        checkboxes.each(function(node) {
            if (node.get('checked')) {
                selecteditems[selecteditems.length] = node.get('value');
            }
        });
        
        if (selecteditems.length === 0) {
            e.preventDefault();
        } else {
            valuecontainer = Y.one('input[name="' + this.selectedcontainer + '"]');
            valuecontainer.set('value', selecteditems.join(','));
        }
    },
    
    toggle_all: function(e) {
        if (e.currentTarget.get('checked')) {
            checkboxes = Y.all('td.c0 input');
            checkboxes.each(function(node) {
                rowelement = node.get('parentNode').get('parentNode');
                node.set('checked', true);
                rowelement.setAttribute('class', 'selectedrow');
            });
        } else {
            checkboxes = Y.all('td.c0 input');
            checkboxes.each(function(node) {
                rowelement = node.get('parentNode').get('parentNode');
                node.set('checked', false);
                rowelement.setAttribute('class', 'unselectedrow');
            });
        }
    },
}
