import Vue from 'vue'
Vue.config.ignoredElements = ['trix-editor']

import ActionSelector from '@/components/ActionSelector'
import BasePartitionMetric from '@/components/Metrics/Base/PartitionMetric'
import BaseTrendMetric from '@/components/Metrics/Base/TrendMetric'
import BaseValueMetric from '@/components/Metrics/Base/ValueMetric'
import Bold from '@/components/Icons/Editor/Bold'
import Card from '@/components/Card'
import Cards from '@/components/Cards'
import CardWrapper from '@/components/CardWrapper'
import Checkbox from '@/components/Index/Checkbox'
import CheckboxWithLabel from '@/components/CheckboxWithLabel'
import ConfirmActionModal from '@/components/Modals/ConfirmActionModal'
import ConfirmUploadRemovalModal from '@/components/Modals/ConfirmUploadRemovalModal'
import CreateResourceButton from '@/components/CreateResourceButton'
import CustomDetailHeader from '@/components/CustomDetailHeader'
import CustomDetailToolbar from '@/components/CustomDetailToolbar'
import CustomIndexHeader from '@/components/CustomIndexHeader'
import CustomIndexToolbar from '@/components/CustomIndexToolbar'
import Delete from '@/components/Icons/Delete'
import DeleteMenu from '@/components/DeleteMenu'
import DeleteResourceModal from '@/components/Modals/DeleteResourceModal'
import Download from '@/components/Icons/Download'
import Dropdown from '@/components/Dropdown'
import DropdownMenu from '@/components/DropdownMenu'
import DropdownTrigger from '@/components/DropdownTrigger'
import Edit from '@/components/Icons/Edit'
import Error403 from '@/views/Error403'
import Error404 from '@/views/Error404'
import Excerpt from '@/components/Excerpt'
import FakeCheckbox from '@/components/Index/FakeCheckbox'
import Filter from '@/components/Icons/Filter'
import FilterMenu from '@/components/FilterMenu'
import ForceDelete from '@/components/Icons/ForceDelete'
import FullScreen from '@/components/Icons/Editor/FullScreen'
import GlobalSearch from '@/components/GlobalSearch'
import Heading from '@/components/Heading'
import HelpCard from '@/components/Cards/HelpCard'
import HelpText from '@/components/Form/HelpText'
import Icon from '@/components/Icons/Icon'
import Image from '@/components/Icons/Editor/Image'
import Index from './views/Index'
import Italic from '@/components/Icons/Editor/Italic'
import Label from '@/components/Form/Label'
import Lens from '@/views/Lens'
import LensSelector from '@/components/LensSelector'
import Link from '@/components/Icons/Editor/Link'
import Loader from '@/components/Icons/Loader'
import LoadingCard from '@/components/LoadingCard'
import LoadingView from '@/components/LoadingView'
import Modal from '@/components/Modal'
import PaginationLinks from '@/components/PaginationLinks'
import PanelItem from '@/components/PanelItem'
import PartitionMetric from '@/components/Metrics/PartitionMetric'
import Play from '@/components/Icons/Play'
import ProgressButton from '@/components/ProgressButton'
import Refresh from '@/components/Icons/Refresh'
import ResourceTable from '@/components/ResourceTable'
import ResourceTableRow from '@/components/Index/ResourceTableRow'
import Restore from '@/components/Icons/Restore'
import RestoreResourceModal from '@/components/Modals/RestoreResourceModal'
import ScrollWrap from '@/components/ScrollWrap'
import Search from '@/components/Icons/Search'
import SearchInput from '@/components/SearchInput'
import SortableIcon from '@/components/Index/SortableIcon'
import TrendMetric from '@/components/Metrics/TrendMetric'
import ValidationErrors from '@/components/ValidationErrors'
import ValueMetric from '@/components/Metrics/ValueMetric'
import View from '@/components/Icons/View'

import SelectFilter from '@/components/Filters/SelectFilter'
import BooleanFilter from '@/components/Filters/BooleanFilter'
import DateFilter from '@/components/Filters/DateFilter'

Vue.component('action-selector', ActionSelector)
Vue.component('base-partition-metric', BasePartitionMetric)
Vue.component('base-trend-metric', BaseTrendMetric)
Vue.component('base-value-metric', BaseValueMetric)
Vue.component('card', Card)
Vue.component('card-wrapper', CardWrapper)
Vue.component('cards', Cards)
Vue.component('checkbox', Checkbox)
Vue.component('checkbox-with-label', CheckboxWithLabel)
Vue.component('confirm-action-modal', ConfirmActionModal)
Vue.component('confirm-upload-removal-modal', ConfirmUploadRemovalModal)
Vue.component('create-resource-button', CreateResourceButton)
Vue.component('custom-detail-header', CustomDetailHeader)
Vue.component('custom-detail-toolbar', CustomDetailToolbar)
Vue.component('custom-index-header', CustomIndexHeader)
Vue.component('custom-index-toolbar', CustomIndexToolbar)
Vue.component('date-filter', DateFilter)
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
Vue.component('excerpt', Excerpt)
Vue.component('fake-checkbox', FakeCheckbox)
Vue.component('filter-menu', FilterMenu)
Vue.component('form-label', Label)
Vue.component('global-search', GlobalSearch)
Vue.component('heading', Heading)
Vue.component('help', HelpCard)
Vue.component('help-text', HelpText)
Vue.component('icon', Icon)
Vue.component('icon-delete', Delete)
Vue.component('icon-download', Download)
Vue.component('icon-edit', Edit)
Vue.component('icon-filter', Filter)
Vue.component('icon-force-delete', ForceDelete)
Vue.component('icon-play', Play)
Vue.component('icon-refresh', Refresh)
Vue.component('icon-restore', Restore)
Vue.component('icon-search', Search)
Vue.component('icon-view', View)
Vue.component('lens', Lens)
Vue.component('lens-selector', LensSelector)
Vue.component('loader', Loader)
Vue.component('loading-card', LoadingCard)
Vue.component('loading-view', LoadingView)
Vue.component('modal', Modal)
Vue.component('pagination-links', PaginationLinks)
Vue.component('panel-item', PanelItem)
Vue.component('partition-metric', PartitionMetric)
Vue.component('progress-button', ProgressButton)
Vue.component('resource-index', Index)
Vue.component('resource-table', ResourceTable)
Vue.component('resource-table-row', ResourceTableRow)
Vue.component('restore-resource-modal', RestoreResourceModal)
Vue.component('scroll-wrap', ScrollWrap)
Vue.component('search-input', SearchInput)
Vue.component('sortable-icon', SortableIcon)
Vue.component('trend-metric', TrendMetric)
Vue.component('validation-errors', ValidationErrors)
Vue.component('value-metric', ValueMetric)

Vue.component('select-filter', SelectFilter)
Vue.component('boolean-filter', BooleanFilter)
