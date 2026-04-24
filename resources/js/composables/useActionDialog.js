import { ref } from 'vue'

export function useActionDialog() {
  const actionDialogRef = ref(null)

  const openAlert = (options = {}) => actionDialogRef.value?.openAlert(options)
  const openConfirm = (options = {}) => actionDialogRef.value?.openConfirm(options)
  const openPrompt = (options = {}) => actionDialogRef.value?.openPrompt(options)
  const openDangerConfirm = (options = {}) => actionDialogRef.value?.openConfirm({ variant: 'danger', ...options })
  const openWarningConfirm = (options = {}) => actionDialogRef.value?.openConfirm({ variant: 'warning', ...options })
  const openSuccessAlert = (options = {}) => actionDialogRef.value?.openAlert({ variant: 'success', ...options })

  return {
    actionDialogRef,
    openAlert,
    openConfirm,
    openPrompt,
    openDangerConfirm,
    openWarningConfirm,
    openSuccessAlert,
  }
}
