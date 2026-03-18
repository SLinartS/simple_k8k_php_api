# CI/CD Pipeline Implementation Plan

## Overview

Add automated CI/CD pipeline using GitHub Actions to build, test, and deploy the PHP API to Kubernetes. Docker images will be pushed to Docker Hub.

## Current State

**Existing project:**
- PHP 8.2 API with nginx/php-fpm
- Docker configuration ready (Dockerfile, nginx config)
- Kubernetes manifests (deployment, service, ingress)
- Deployed to Minikube cluster

**Missing:**
- No CI/CD configuration
- No automated testing
- No image versioning strategy
- No automated deployment workflow

## Proposed Design

### Pipeline Stages

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Lint &    │───>│   Build &   │───>│    Push     │───>│   Deploy    │
│   Test      │    │   Test      │    │   Image     │    │   to K8s    │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

### Trigger Strategy

| Branch | Trigger | Action |
|--------|---------|--------|
| `main` | Push/PR | Build, test, push `latest`, deploy |
| `develop` | Push/PR | Build, test, push `develop` tag |
| Feature branches | PR only | Build, test (no push/deploy) |

### Image Tagging

- `latest` - latest stable build from main
- `v1.0.0` - semantic version tags
- `main-abc123` - branch-sha for traceability

### Security

- Secrets stored in GitHub repository secrets
- No hardcoded credentials
- Minimal permissions principle

## Implementation Plan

### Phase 1: GitHub Actions Workflow

**Files to create:**
1. `.github/workflows/ci-cd.yaml` - Main pipeline
2. `.github/workflows/pr-check.yaml` - PR validation (optional, can merge into main)

**Steps:**
1. Create `.github/workflows/` directory
2. Create main CI/CD workflow with jobs:
   - `lint` - PHP syntax check
   - `build` - Build Docker image
   - `test` - Run container and test endpoints
   - `push` - Push to Docker Hub (main branch only)
   - `deploy` - Deploy to Kubernetes (main branch only, manual approval optional)

### Phase 2: Kubernetes Update

**Files to modify:**
1. `k8s/deployment.yaml` - Update image reference to use Docker Hub

**Changes:**
- Change `image: php-api:latest` to `image: ${DOCKER_USERNAME}/php-api:latest`
- Add image pull policy configuration

### Phase 3: Documentation Update

**Files to modify:**
1. `README.md` - Add CI/CD section

**Content:**
- Required GitHub secrets
- Branch workflow explanation
- Manual deployment instructions

## Required GitHub Secrets

| Secret Name | Description |
|-------------|-------------|
| `DOCKER_USERNAME` | Docker Hub username |
| `DOCKER_PASSWORD` | Docker Hub access token |
| `KUBE_CONFIG` | Base64 encoded kubeconfig (for deployment) |

## File Structure After Implementation

```
.github/
└── workflows/
    └── ci-cd.yaml      # Main CI/CD pipeline
k8s/
├── deployment.yaml     # Updated image reference
├── service.yaml        # No changes
└── ingress.yaml        # No changes
```

## Risk Assessment

| Risk | Impact | Mitigation |
|------|--------|------------|
| Docker Hub rate limits | Medium | Use GitHub Container Registry as backup |
| Kubeconfig exposure | High | Use GitHub secrets, consider OIDC |
| Failed deployments | Medium | Add rollback capability, health checks |

## Testing Strategy

1. **Lint stage** - PHP syntax validation (`php -l`)
2. **Build stage** - Docker build succeeds
3. **Test stage** - Curl health endpoint in running container
4. **Deploy stage** - Verify pods are healthy after deployment

## Complexity Estimate

- **Time:** 30-45 minutes
- **Difficulty:** Medium
- **Dependencies:** Docker Hub account, GitHub repository

## Decisions

- **Deployment:** Automatic on main push (no manual approval)
- **Versioning:** Basic tagging (`latest` + `main-<sha>`)

## Ready for Implementation

Plan approved. Switch to Code agent to implement.
