FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache nginx

# Copy application
COPY app /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Copy nginx configuration
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Expose port
EXPOSE 80

# Start both nginx and php-fpm
CMD ["/start.sh"]
