/**
 * JoomCCK Signature Field JavaScript
 * Handles signature capture using HTML5 Canvas
 */

var JoomCCKSignature = {
    fields: {},
    
    /**
     * Initialize a signature field
     */
    initField: function(fieldId) {
        var canvas = document.getElementById('signature_canvas_' + fieldId);
        if (!canvas) {
            console.error('Signature canvas not found for field ' + fieldId);
            return;
        }
        
        var field = {
            id: fieldId,
            canvas: canvas,
            ctx: canvas.getContext('2d'),
            isDrawing: false,
            lastX: 0,
            lastY: 0,
            strokes: [],
            currentStroke: [],
            penColor: canvas.dataset.penColor || '#000000',
            penWidth: parseInt(canvas.dataset.penWidth) || 2,
            mobilePenWidth: parseInt(canvas.dataset.mobilePenWidth) || 3,
            touchEnabled: canvas.dataset.touchEnabled === '1',
            minStrokes: parseInt(canvas.dataset.minStrokes) || 1,
            responsive: canvas.dataset.responsive === '1'
        };
        
        // Store field reference
        this.fields[fieldId] = field;
        
        // Set up canvas
        this.setupCanvas(field);
        
        // Bind events
        this.bindEvents(field);
        
        // Handle responsive canvas
        if (field.responsive) {
            this.makeResponsive(field);
        }
        
        // Update status
        this.updateStatus(fieldId);
    },
    
    /**
     * Setup canvas properties
     */
    setupCanvas: function(field) {
        var ctx = field.ctx;
        
        // Set line properties
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = field.penColor;
        ctx.lineWidth = this.isMobile() ? field.mobilePenWidth : field.penWidth;
        
        // Enable smoothing
        ctx.imageSmoothingEnabled = true;
    },
    
    /**
     * Bind mouse and touch events
     */
    bindEvents: function(field) {
        var canvas = field.canvas;
        var self = this;
        
        // Mouse events
        canvas.addEventListener('mousedown', function(e) {
            self.startDrawing(field, e);
        });
        
        canvas.addEventListener('mousemove', function(e) {
            self.draw(field, e);
        });
        
        canvas.addEventListener('mouseup', function(e) {
            self.stopDrawing(field, e);
        });
        
        canvas.addEventListener('mouseout', function(e) {
            self.stopDrawing(field, e);
        });
        
        // Touch events (if enabled)
        if (field.touchEnabled) {
            canvas.addEventListener('touchstart', function(e) {
                e.preventDefault();
                var touch = e.touches[0];
                var mouseEvent = new MouseEvent('mousedown', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                canvas.dispatchEvent(mouseEvent);
            });
            
            canvas.addEventListener('touchmove', function(e) {
                e.preventDefault();
                var touch = e.touches[0];
                var mouseEvent = new MouseEvent('mousemove', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                canvas.dispatchEvent(mouseEvent);
            });
            
            canvas.addEventListener('touchend', function(e) {
                e.preventDefault();
                var mouseEvent = new MouseEvent('mouseup', {});
                canvas.dispatchEvent(mouseEvent);
            });
        }
        
        // Prevent scrolling when touching the canvas
        document.body.addEventListener('touchstart', function(e) {
            if (e.target === canvas) {
                e.preventDefault();
            }
        }, {passive: false});
        
        document.body.addEventListener('touchend', function(e) {
            if (e.target === canvas) {
                e.preventDefault();
            }
        }, {passive: false});
        
        document.body.addEventListener('touchmove', function(e) {
            if (e.target === canvas) {
                e.preventDefault();
            }
        }, {passive: false});
    },
    
    /**
     * Start drawing
     */
    startDrawing: function(field, e) {
        field.isDrawing = true;
        
        var rect = field.canvas.getBoundingClientRect();
        var scaleX = field.canvas.width / rect.width;
        var scaleY = field.canvas.height / rect.height;
        
        field.lastX = (e.clientX - rect.left) * scaleX;
        field.lastY = (e.clientY - rect.top) * scaleY;
        
        // Start new stroke
        field.currentStroke = [{
            x: field.lastX,
            y: field.lastY,
            type: 'start'
        }];
        
        // Hide placeholder
        this.hidePlaceholder(field.id);
    },
    
    /**
     * Draw on canvas
     */
    draw: function(field, e) {
        if (!field.isDrawing) return;
        
        var rect = field.canvas.getBoundingClientRect();
        var scaleX = field.canvas.width / rect.width;
        var scaleY = field.canvas.height / rect.height;
        
        var currentX = (e.clientX - rect.left) * scaleX;
        var currentY = (e.clientY - rect.top) * scaleY;
        
        // Draw line
        field.ctx.beginPath();
        field.ctx.moveTo(field.lastX, field.lastY);
        field.ctx.lineTo(currentX, currentY);
        field.ctx.stroke();
        
        // Add point to current stroke
        field.currentStroke.push({
            x: currentX,
            y: currentY,
            type: 'line'
        });
        
        // Update last position
        field.lastX = currentX;
        field.lastY = currentY;
    },
    
    /**
     * Stop drawing
     */
    stopDrawing: function(field, e) {
        if (!field.isDrawing) return;
        
        field.isDrawing = false;
        
        // Save current stroke
        if (field.currentStroke.length > 1) {
            field.strokes.push(field.currentStroke);
        }
        
        // Update signature data
        this.updateSignatureData(field.id);
        
        // Update status
        this.updateStatus(field.id);
    },
    
    /**
     * Clear canvas
     */
    clearCanvas: function(fieldId) {
        var field = this.fields[fieldId];
        if (!field) return;
        
        // Clear canvas
        field.ctx.clearRect(0, 0, field.canvas.width, field.canvas.height);
        
        // Reset strokes
        field.strokes = [];
        field.currentStroke = [];
        
        // Clear signature data
        document.getElementById('signature_data_' + fieldId).value = '';
        
        // Show placeholder
        this.showPlaceholder(fieldId);
        
        // Update status
        this.updateStatus(fieldId);
    },
    
    /**
     * Undo last stroke
     */
    undoStroke: function(fieldId) {
        var field = this.fields[fieldId];
        if (!field || field.strokes.length === 0) return;
        
        // Remove last stroke
        field.strokes.pop();
        
        // Redraw canvas
        this.redrawCanvas(field);
        
        // Update signature data
        this.updateSignatureData(fieldId);
        
        // Update status
        this.updateStatus(fieldId);
        
        // Show placeholder if no strokes
        if (field.strokes.length === 0) {
            this.showPlaceholder(fieldId);
        }
    },
    
    /**
     * Redraw canvas from strokes
     */
    redrawCanvas: function(field) {
        var ctx = field.ctx;
        
        // Clear canvas
        ctx.clearRect(0, 0, field.canvas.width, field.canvas.height);
        
        // Redraw all strokes
        for (var i = 0; i < field.strokes.length; i++) {
            var stroke = field.strokes[i];
            if (stroke.length < 2) continue;
            
            ctx.beginPath();
            ctx.moveTo(stroke[0].x, stroke[0].y);
            
            for (var j = 1; j < stroke.length; j++) {
                ctx.lineTo(stroke[j].x, stroke[j].y);
            }
            
            ctx.stroke();
        }
    },
    
    /**
     * Update signature data (base64)
     */
    updateSignatureData: function(fieldId) {
        var field = this.fields[fieldId];
        if (!field) return;
        
        // Get canvas data as base64
        var dataURL = field.canvas.toDataURL('image/png');
        
        // Store in hidden input
        document.getElementById('signature_data_' + fieldId).value = dataURL;
    },
    
    /**
     * Update status message
     */
    updateStatus: function(fieldId) {
        var field = this.fields[fieldId];
        var statusEl = document.getElementById('signature_status_' + fieldId);
        
        if (!field || !statusEl) return;
        
        var strokeCount = field.strokes.length;
        var message = '';
        
        if (strokeCount === 0) {
            message = '<span style="color: #999;">No signature drawn</span>';
        } else if (strokeCount < field.minStrokes) {
            message = '<span style="color: #f39c12;">Signature too simple (minimum ' + field.minStrokes + ' strokes)</span>';
        } else {
            message = '<span style="color: #27ae60;">Signature ready (' + strokeCount + ' strokes)</span>';
        }
        
        statusEl.innerHTML = message;
    },
    
    /**
     * Show placeholder text
     */
    showPlaceholder: function(fieldId) {
        var placeholder = document.getElementById('signature_placeholder_' + fieldId);
        if (placeholder) {
            placeholder.style.display = 'block';
        }
    },
    
    /**
     * Hide placeholder text
     */
    hidePlaceholder: function(fieldId) {
        var placeholder = document.getElementById('signature_placeholder_' + fieldId);
        if (placeholder) {
            placeholder.style.display = 'none';
        }
    },
    
    /**
     * Show canvas for editing
     */
    showCanvas: function(fieldId) {
        var existing = document.getElementById('signature_existing_' + fieldId);
        var container = document.getElementById('signature_canvas_container_' + fieldId);
        
        if (existing) existing.style.display = 'none';
        if (container) container.style.display = 'block';
        
        // Clear any existing signature data to force new signature
        document.getElementById('signature_data_' + fieldId).value = '';
    },
    
    /**
     * Keep existing signature
     */
    keepExisting: function(fieldId) {
        var existing = document.getElementById('signature_existing_' + fieldId);
        var container = document.getElementById('signature_canvas_container_' + fieldId);
        
        if (existing) existing.style.display = 'block';
        if (container) container.style.display = 'none';
        
        // Clear new signature data
        document.getElementById('signature_data_' + fieldId).value = '';
    },
    
    /**
     * Make canvas responsive
     */
    makeResponsive: function(field) {
        var canvas = field.canvas;
        var container = canvas.parentElement;
        
        // Store original dimensions
        var originalWidth = canvas.width;
        var originalHeight = canvas.height;
        var aspectRatio = originalHeight / originalWidth;
        
        var resizeCanvas = function() {
            var containerWidth = container.offsetWidth;
            var maxWidth = Math.min(containerWidth - 20, originalWidth); // 20px padding
            
            // Calculate new dimensions maintaining aspect ratio
            var newWidth = maxWidth;
            var newHeight = newWidth * aspectRatio;
            
            // Ensure minimum size for usability
            if (newHeight < 180) {
                newHeight = 180;
                newWidth = newHeight / aspectRatio;
            }
            
            // Apply CSS dimensions for display
            canvas.style.width = newWidth + 'px';
            canvas.style.height = newHeight + 'px';
            
            // Keep canvas internal dimensions for drawing accuracy
            canvas.width = originalWidth;
            canvas.height = originalHeight;
            
            // Redraw existing content
            field.ctx.lineCap = 'round';
            field.ctx.lineJoin = 'round';
            field.ctx.strokeStyle = field.penColor;
            field.ctx.lineWidth = JoomCCKSignature.isMobile() ? field.mobilePenWidth : field.penWidth;
        };
        
        // Initial resize
        resizeCanvas();
        
        // Resize on window resize with debouncing
        var resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(resizeCanvas, 100);
        });
    },
    
    /**
     * Check if device is mobile
     */
    isMobile: function() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    },
    
    /**
     * Validate signature field
     */
    validateField: function(fieldId) {
        var field = this.fields[fieldId];
        var dataInput = document.getElementById('signature_data_' + fieldId);
        var fileInput = document.getElementById('signature_file_' + fieldId);
        
        if (!field) return true;
        
        // Check if signature is required
        var container = document.getElementById('signature_container_' + fieldId);
        var isRequired = container && container.classList.contains('required');
        
        if (!isRequired) return true;
        
        // Check if we have signature data or existing file
        var hasData = dataInput && dataInput.value;
        var hasFile = fileInput && fileInput.value;
        var hasMinStrokes = field.strokes.length >= field.minStrokes;
        
        return (hasData && hasMinStrokes) || hasFile;
    }
};

// Global validation function for form submission
window.validateSignatureField = function(fieldId) {
    return JoomCCKSignature.validateField(fieldId);
};