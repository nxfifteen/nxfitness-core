<% if(logo) { %><img width="145px" src="<%= logo %>" /><%= '\n\n' %><% } %># <%= title %>
<% if(intro) { %><%= '\n' %>_<%= intro %>_<%= '\n' %><% } %>
<% if(version) { %>## <%= version.name %> <%= version.number %> ( <%= version.date %> )<%= '\n' %><% } %>
<% _.forEach(sections, function(section) { %>
<% if(section.commitsCount > 0) { %>
## <%= section.title %>
<% _.forEach(section.commits, function(commit){ %>  - <%= commit.subject %> (<%= getCommitLinks(commit) %>)<% if(commit.closes.length){ %><% if(section.title == "WIP"){ %>, Issue: <%= getCommitCloses(commit).join(',') %><% } else { %>, Closes: <%= getCommitCloses(commit).join(',') %><% } %><% } %>
<% }) %><% _.forEach(section.components, function(component){ %>  - **<%= component.name %>**
<% _.forEach(component.commits, function(commit){ %>    - <%= commit.subject %> <%= commit.hash %> <% if(commit.closes.length){ %>, Closes: <%= getCommitCloses(commit).join(',') %><% } %><% }) %>
<% }) %>
<% } %>
<% }) %>

---
<sub><sup>*Generated with [git-changelog](https://github.com/rafinskipg/git-changelog). If you have any problems or suggestions, create an issue.* :) **Thanks** </sub></sup>
