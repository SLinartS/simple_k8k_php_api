# Implementation Plan: Simple PHP API with Kubernetes Deployment

## Overview

**Objective**: Create a simple PHP REST API application and automate its deployment to Kubernetes.

**Approach**: 
- Build a lightweight PHP application without frameworks
- Containerize using Docker with nginx and php-fpm
- Deploy to Kubernetes using declarative manifests

## Current State

**Environment**: 
- Empty project directory at `/home/linart/personal/projects/simple_k8k_project`
- No existing code or infrastructure
- Not a git repository

## Proposed Design

### Architecture

```
[User] → [Ingress] → [Service] → [Pod (nginx + php-fpm)] → [PHP Application]
```

### Components

1. **PHP Application**: REST API with 4 endpoints
2. **Container**: Alpine-based with nginx + php-fpm
3. **Kubernetes**: Deployment with 2 replicas, Service, Ingress

## Implementation Plan

### Phase 1: Application Layer ✓
**Status**: Completed

**Files Created**:
- `app/index.php` - Simple REST API router

**Endpoints**:
- `GET /` - API info
- `GET /health` - Health check (for Kubernetes probes)
- `GET /hello` - Greeting
- `GET /hello/{name}` - Personalized greeting

### Phase 2: Container Layer ✓
**Status**: Completed

**Files Created**:
- `Dockerfile` - Multi-stage build with nginx and php-fpm
- `docker/nginx/default.conf` - Nginx virtual host configuration
- `docker/start.sh` - Startup script for running both processes

**Design Decisions**:
- Used `php:8.2-fpm-alpine` for minimal image size
- Single container runs both nginx and php-fpm (simpler than sidecar pattern)
- Alpine Linux reduces attack surface and image size

### Phase 3: Kubernetes Layer ✓
**Status**: Completed

**Files Created**:
- `k8s/deployment.yaml` - Application deployment
- `k8s/service.yaml` - Internal service discovery
- `k8s/ingress.yaml` - External access configuration

**Configuration**:
- **Deployment**: 2 replicas, resource limits (128Mi/100m), health probes
- **Service**: ClusterIP on port 80
- **Ingress**: nginx class, host `php-api.local`

### Phase 4: Documentation ✓
**Status**: Completed

**Files Created**:
- `README.md` - Complete deployment guide

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Nginx Ingress Controller not installed | Medium | High | Document installation steps in README |
| Image not available in cluster | Medium | High | Document `minikube image load` / `kind load` |
| DNS resolution for local development | Medium | Medium | Document `/etc/hosts` modification |

## Testing Strategy

### Local Testing
```bash
# Build image
docker build -t php-api:latest .

# Test locally
docker run -p 8080:80 php-api:latest
curl http://localhost:8080/health
```

### Kubernetes Testing
```bash
# Apply manifests
kubectl apply -f k8s/

# Verify deployment
kubectl get pods -l app=php-api
kubectl logs -l app=php-api

# Test endpoints
curl http://php-api.local/health
```

### Acceptance Criteria
- [x] Application responds to all defined endpoints
- [x] Health endpoint returns proper JSON
- [x] Docker image builds successfully
- [x] Kubernetes manifests are valid
- [x] Documentation includes all deployment steps

## Summary

### What Was Created

1. **PHP Application** (`app/index.php`)
   - Simple REST API without frameworks
   - 4 endpoints: /, /health, /hello, /hello/{name}
   - Returns JSON responses

2. **Docker Configuration**
   - `Dockerfile`: Alpine-based image with nginx + php-fpm
   - `docker/nginx/default.conf`: Nginx virtual host routing to php-fpm
   - `docker/start.sh`: Startup script for multi-process container

3. **Kubernetes Manifests**
   - `k8s/deployment.yaml`: 2-replica deployment with health probes
   - `k8s/service.yaml`: ClusterIP service for internal routing
   - `k8s/ingress.yaml`: Ingress for external access via nginx controller

4. **Documentation**
   - `README.md`: Complete deployment guide with prerequisites

### Next Steps (Optional Enhancements)
- Add ConfigMap for environment-specific configuration
- Add HorizontalPodAutoscaler for auto-scaling
- Add CI/CD pipeline configuration
- Add monitoring/logging stack
