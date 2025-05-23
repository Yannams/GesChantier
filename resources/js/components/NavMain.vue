<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import SidebarMenuSub from './ui/sidebar/SidebarMenuSub.vue';
import SidebarMenuSubItem from './ui/sidebar/SidebarMenuSubItem.vue';
import SidebarMenuSubButton from './ui/sidebar/SidebarMenuSubButton.vue';
import Tooltip from './ui/tooltip/Tooltip.vue';
import Collapsible from './ui/collapsible/Collapsible.vue';
import CollapsibleTrigger from './ui/collapsible/CollapsibleTrigger.vue';
import CollapsibleContent from './ui/collapsible/CollapsibleContent.vue';
import Button from './ui/button/Button.vue';


defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();

function toggleItem(item: NavItem) {
  item.isOpen = !item.isOpen;
  console.log(item.isOpen);
  
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <Collapsible defaultOpen class="group/collapsible"> 
                        <SidebarMenuButton 
                                as-child :is-active="item.href === page.url"
                                :tooltip="item.title"
                                v-if="!item.children"
                            >
                                <Link :href="item.href" @click="toggleItem(item)">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                        </SidebarMenuButton>
                        <CollapsibleTrigger asChild  v-else>
                            <SidebarMenuButton 
                                as-child :is-active="item.href === page.url"
                                :tooltip="item.title"
                            
                            >
                                <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </div>
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub v-if="item.children?.length">
                                <SidebarMenuSubItem v-for= "subItem in item.children" :key="subItem.title">
                                    <SidebarMenuSubButton
                                        as-child :is-active="subItem.href===page.url"
                                        :tooltip="subItem.title"
                                    >
                                        <Link :href="subItem.href">
                                            <component :is="subItem.icon" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>       
                    </Collapsible>
                 </SidebarMenuItem>
            </SidebarMenu>
        
        
    </SidebarGroup>
</template>
