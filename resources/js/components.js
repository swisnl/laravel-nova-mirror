import Vue from 'vue'

import ActionSelector from '@/components/ActionSelector'
import BasePartitionMetric from '@/components/Metrics/Base/PartitionMetric'
import BaseTrendMetric from '@/components/Metrics/Base/TrendMetric'
import BaseValueMetric from '@/components/Metrics/Base/ValueMetric'
import Card from '@/components/Card'
import HelpCard from '@/components/Cards/HelpCard'
import Cards from '@/components/Cards'
import CardWrapper from '@/components/CardWrapper'
import Checkbox from '@/components/Index/Checkbox'
import ConfirmActionModal from '@/components/Modals/ConfirmActionModal'
import ConfirmUploadRemovalModal from '@/components/Modals/ConfirmUploadRemovalModal'
import CreateResourceButton from '@/components/CreateResourceButton'
import DeleteMenu from '@/components/DeleteMenu'
import DeleteResourceModal from '@/components/Modals/DeleteResourceModal'
import Dropdown from '@/components/Dropdown'
import DropdownMenu from '@/components/DropdownMenu'
import DropdownTrigger from '@/components/DropdownTrigger'
import Error404 from '@/views/Error404'
import Error403 from '@/views/Error403'
import Bold from '@/components/Icons/Editor/Bold'
import FullScreen from '@/components/Icons/Editor/FullScreen'
import GlobalSearch from '@/components/GlobalSearch'
import Image from '@/components/Icons/Editor/Image'
import Italic from '@/components/Icons/Editor/Italic'
import Link from '@/components/Icons/Editor/Link'
import FakeCheckbox from '@/components/Index/FakeCheckbox'
import FilterSelect from '@/components/FilterSelect'
import FilterSelector from '@/components/FilterSelector'
import Label from '@/components/Form/Label'
import Heading from '@/components/Heading'
import HelpText from '@/components/Form/HelpText'
import Icon from '@/components/Icons/Icon'
import ForceDelete from '@/components/Icons/ForceDelete'
import Delete from '@/components/Icons/Delete'
import Download from '@/components/Icons/Download'
import Edit from '@/components/Icons/Edit'
import Filter from '@/components/Icons/Filter'
import Play from '@/components/Icons/Play'
import Restore from '@/components/Icons/Restore'
import Refresh from '@/components/Icons/Refresh'
import Search from '@/components/Icons/Search'
import View from '@/components/Icons/View'
import Lens from '@/views/Lens'
import LensSelector from '@/components/LensSelector'
import LoadingCard from '@/components/LoadingCard'
import LoadingView from '@/components/LoadingView'
import Loader from '@/components/Icons/Loader'
import Modal from '@/components/Modal'
import PaginationLinks from '@/components/PaginationLinks'
import PanelItem from '@/components/PanelItem'
import PartitionMetric from '@/components/Metrics/PartitionMetric'
import Index from './views/Index'
import ResourceTable from '@/components/ResourceTable'
import ResourceTableRow from '@/components/Index/ResourceTableRow'
import RestoreResourceModal from '@/components/Modals/RestoreResourceModal'
import SearchInput from '@/components/SearchInput'
import SortableIcon from '@/components/Index/SortableIcon'
import TrendMetric from '@/components/Metrics/TrendMetric'
import ValidationErrors from '@/components/ValidationErrors'
import ValueMetric from '@/components/Metrics/ValueMetric'

Vue.component('action-selector', ActionSelector)
Vue.component('base-partition-metric', BasePartitionMetric)
Vue.component('base-trend-metric', BaseTrendMetric)
Vue.component('base-value-metric', BaseValueMetric)
Vue.component('card', Card)
Vue.component('help', HelpCard)
Vue.component('cards', Cards)
Vue.component('card-wrapper', CardWrapper)
Vue.component('checkbox', Checkbox)
Vue.component('confirm-action-modal', ConfirmActionModal)
Vue.component('confirm-upload-removal-modal', ConfirmUploadRemovalModal)
Vue.component('create-resource-button', CreateResourceButton)
Vue.component('delete-menu', DeleteMenu)
Vue.component('delete-resource-modal', DeleteResourceModal)
Vue.component('dropdown', Dropdown)
Vue.component('dropdown-menu', DropdownMenu)
Vue.component('dropdown-trigger', DropdownTrigger)
Vue.component('editor-bold', Bold)
Vue.component('editor-fullscreen', FullScreen)
Vue.component('editor-image', Image)
Vue.component('editor-italic', Italic)
Vue.component('editor-link', Link)
Vue.component('error-403', Error403)
Vue.component('error-404', Error404)
Vue.component('fake-checkbox', FakeCheckbox)
Vue.component('filter-select', FilterSelect)
Vue.component('filter-selector', FilterSelector)
Vue.component('form-label', Label)
Vue.component('heading', Heading)
Vue.component('help-text', HelpText)
Vue.component('global-search', GlobalSearch)
Vue.component('icon', Icon)
Vue.component('icon-force-delete', ForceDelete)
Vue.component('icon-delete', Delete)
Vue.component('icon-download', Download)
Vue.component('icon-edit', Edit)
Vue.component('icon-filter', Filter)
Vue.component('icon-play', Play)
Vue.component('icon-restore', Restore)
Vue.component('icon-refresh', Refresh)
Vue.component('icon-search', Search)
Vue.component('icon-view', View)
Vue.component('lens', Lens)
Vue.component('lens-selector', LensSelector)
Vue.component('loading-card', LoadingCard)
Vue.component('loading-view', LoadingView)
Vue.component('loader', Loader)
Vue.component('modal', Modal)
Vue.component('pagination-links', PaginationLinks)
Vue.component('panel-item', PanelItem)
Vue.component('partition-metric', PartitionMetric)
Vue.component('resource-index', Index)
Vue.component('resource-table', ResourceTable)
Vue.component('resource-table-row', ResourceTableRow)
Vue.component('restore-resource-modal', RestoreResourceModal)
Vue.component('search-input', SearchInput)
Vue.component('sortable-icon', SortableIcon)
Vue.component('trend-metric', TrendMetric)
Vue.component('validation-errors', ValidationErrors)
Vue.component('value-metric', ValueMetric)
