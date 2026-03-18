#!/bin/bash
# Forward local port 8443 to Minikube API server
# Requires: sudo apt install socat

MINIKUBE_IP="192.168.49.2"
LOCAL_PORT="8443"

echo "Forwarding port $LOCAL_PORT to Minikube at $MINIKUBE_IP:$LOCAL_PORT"
exec socat TCP-LISTEN:$LOCAL_PORT,fork,reuseaddr TCP:$MINIKUBE_IP:$LOCAL_PORT
